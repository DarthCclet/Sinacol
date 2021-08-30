<?php

namespace App\Console\Commands;

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Audiencia;
use App\AudienciaParte;
use App\Compareciente;
use App\ConceptoPagoResolucion;
use App\ConciliadorAudiencia;
use App\Documento;
use App\EtapaResolucionAudiencia;
use App\Events\GenerateDocumentResolution;
use App\Expediente;
use App\Http\Controllers\ContadorController;
use App\Parte;
use App\ResolucionPagoDiferido;
use App\ResolucionParteConcepto;
use App\ResolucionPartes;
use App\Sala;
use App\SalaAudiencia;
use App\Solicitud;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Lee información de un archivo xlsx y crea convenios inmediatos masivamente.
 * @package App\Console\Commands
 */
class ConveniosMasivos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convenioMasivo {nombre : Path al archivo xlsx que trae los datos de los convenios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para capturar masivamente los convenios de solicitudes previamente capturados';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var string Nombre del archivo xlsx
     */
    protected $nombreArchivo;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $savedConv = fopen(__DIR__."/../../../public/savedConv.txt", 'w');
        $failedConv = fopen(__DIR__."/../../../public/failedConv.txt", 'w');
        $ratificConv = fopen(__DIR__."/../../../public/ratificConv.txt", 'w');
        $nombreArchivo = $this->argument('nombre');
        $this->nombreArchivo = $nombreArchivo;
        $archivo = __DIR__."/../../../".$nombreArchivo;
        $existe = file_exists($archivo);

        if(!empty($nombreArchivo) && $existe){

            // Obtenemos el documento que contiene las CURP
            $arreglo = $this->obtenerCurp($archivo);

            // Se obtienen los ids de conceptos
            $array_conceptos = $this->obtenerConceptos($archivo);

            if(empty($array_conceptos)){
                $error = "Los conceptos no esta correctamente configurados";
                $this->error($error);
                fputs($failedConv, $error."\n");
                return;
            }

            $conciliador = $this->obtenerDatosConciliador($archivo);
            if(empty($conciliador)){
                $error = "No se encontró al conciliador, favor de revisar que el nombre coincida como está dado de alta";
                $this->error($error);
                fputs($failedConv, $error."\n");
                return;
            }
            // Recorremos todas las curp
            $array = [];
            foreach ($arreglo as $key => $curp) {
            //Localizamos la Parte para obtener la solicitud
                $parte = Parte::whereCurp($curp)->first();
            //Aquí comienza el proceso de confirmación y generación del expediente
                if ($parte != null) {
                    $solicitud = $parte->solicitud;
                    if($solicitud->expediente == null){
                        $registro = $this->ConfirmarSolicitudMultiple($solicitud,$curp,$array_conceptos,$conciliador);
                        // dd($registro);
                        if($registro["exito"]){
                            $array[] = array("solicitud_id" => $solicitud->id , "audiencia_id" => $registro["audiencia_id"],"curp" => $curp);
                            $correcto = "Se ratifico la solicitud con el folio: ".$solicitud->folio."/".$solicitud->anio;
                            dump($correcto);
                            fputs($savedConv, $correcto."\n");
                        }else{
                            $error = "Ocurrio un error en la solicitud con el folio: ".$solicitud->folio."/".$solicitud->anio;
                            dump($error);
                            fputs($failedConv, $error."\n");
                        }
                    }else{
                        $error = " La solicitud con el folio: ".$solicitud->folio."/".$solicitud->anio. " Ya esta ratificada";
                        dump($error);
                        fputs($ratificConv, $error."\n");
                    }
                }else{
                    $error = "Se encontro una curp erronea en la linea ".($key+1);
                    dump($error);
                    fputs($failedConv, $error."\n");
                }
            }
            dd($array);
        }else{
            $this->error("No se encontró el archivo");
        }
    }

    /**
     * Confirma una solicitud
     * @param Solicitud $solicitud La solicitud a confirmar
     * @param string $curp La CURP del citado
     * @param array $array_conceptos Arreglo de conceptos
     * @param array $conciliador Datos del conciliador que llevó la conciliación
     * @return array
     */
    private function ConfirmarSolicitudMultiple(Solicitud $solicitud,$curp,$array_conceptos,$conciliador) {
        try {
            DB::beginTransaction();
            //obtenemos los folios
            $ContadorController = new ContadorController();
            $folioC = $ContadorController->getContador(1, $solicitud->centro->id);
            $folioAudiencia = $ContadorController->getContador(3, 15);

            // Colocamos los parametros en variables
            $tipoParte = \App\TipoParte::whereNombre("SOLICITANTE")->first();

            list($fecha_audiencia, $hora_inicio_audiencia, $hora_fin_audiencia, $fecha_resolucion) = $this->getDatosFechasAudiencia();

            // Obtenemos la sala virtual
            $sala = Sala::where("centro_id", $solicitud->centro_id)->where("virtual", true)->first();
            if ($sala == null) {
                DB::rollBack();
                return array("exito" => false,"audiencia_id" => null);
            }
            $sala_id = $sala->id;

            if ($conciliador == null) {
                DB::rollBack();
                return array("exito" => false,"audiencia_id" => null);
            }

            // Validamos si ya hay un expediente de la solicitud
            if ($solicitud->expediente == null) {
                // Creamos la estructura del folio
                $edo_folio = $solicitud->centro->abreviatura;
                $folio = $edo_folio . "/CJ/I/" . $folioC->anio . "/" . sprintf("%06d", $folioC->contador);
                // Creamos el expediente de la solicitud
                $expediente = Expediente::create(["solicitud_id" => $solicitud->id, "folio" => $folio, "anio" => $folioC->anio, "consecutivo" => $folioC->contador]);
                // Indicamos que el solicitante esta ratificando
                foreach ($solicitud->partes as $key => $parte) {
                    if ($tipoParte->id == $parte->tipo_parte_id) {
                        $parte->update(["ratifico" => true]);
                    }
                }
                // Modificamos la solicitud para indicar que ya se ratifico
                $solicitud->update(["estatus_solicitud_id" => 3, "url_virtual" => null, "ratificada" => true, "fecha_ratificacion" => now(), "inmediata" => true]);

                // Hacemos el registro de la audiencia
                $audiencia = Audiencia::create([
                    "expediente_id" => $expediente->id,
                    "multiple" => false,
                    "fecha_audiencia" => $fecha_audiencia,
                    "hora_inicio" => $hora_inicio_audiencia,
                    "hora_fin" => $hora_fin_audiencia,
                    "conciliador_id" => $conciliador->id,
                    "numero_audiencia" => 1,
                    "reprogramada" => false,
                    "anio" => $folioAudiencia->anio,
                    "folio" => $folioAudiencia->contador,
                    "fecha_cita" => null,
                    "finalizada" => true,
                    "solicitud_cancelacion" => false,
                    "cancelacion_atendida" => false,
                    "encontro_audiencia" => true,
                    "tipo_terminacion_audiencia" => 1,
                    "audiencia_creada" => false,
                    "fecha_resolucion" => $fecha_resolucion,
                    "resolucion_id" => 1
                ]);

                // guardamos la sala y el conciliador a la audiencia
                ConciliadorAudiencia::create(["audiencia_id" => $audiencia->id, "conciliador_id" => $conciliador->id, "solicitante" => true]);
                SalaAudiencia::create(["audiencia_id" => $audiencia->id, "sala_id" => $sala_id, "solicitante" => true]);

                // Registramos las partes a la audiencia
                foreach ($solicitud->partes as $parte) {
                    AudienciaParte::create(["audiencia_id" => $audiencia->id, "parte_id" => $parte->id, "tipo_notificacion_id" => null]);
                    if ($parte->tipo_parte_id == 2) {
                        // generar citatorio de conciliacion
                        event(new GenerateDocumentResolution($audiencia->id, $solicitud->id, 14, 4, null, $parte->id));
                    }
                }

                // Eliminamos el acuse de la solicitud
                $acuse = Documento::where('documentable_type', 'App\Solicitud')->where('documentable_id', $solicitud->id)->where('clasificacion_archivo_id', 40)->first();
                if ($acuse != null) {
                    $acuse->delete();
                }

                // Creamos el nuevo acuse
                event(new GenerateDocumentResolution("", $solicitud->id, 40, 6));


                // Registramos el representante legal
                $parteRepresentada = null;
                foreach($solicitud->partes as $parte){
                    if($parte->tipo_parte_id == 1){
                        $parteRepresentada = $parte->id;
                    }
                }

                $representante_data = $this->obtenerDatosRepresentante();
                $genero = $representante_data[4] =="H" ? 1 : 2 ;
                $representante = Parte::create([
                    "solicitud_id" => $solicitud->id,
                    "tipo_parte_id" => 3,
                    "tipo_persona_id" => 1,
                    "rfc" => "",
                    "curp" => $representante_data[6],
                    "nombre" => $representante_data[0],
                    "primer_apellido" => $representante_data[1],
                    "segundo_apellido" => $representante_data[2],
                    "fecha_nacimiento" => $representante_data[3],
                    "genero_id" => $genero,
                    "clasificacion_archivo_id" => null,
                    "detalle_instrumento" => null,
                    "feha_instrumento" => $representante_data[5],
                    "detalle_instrumento" => null,
                    "parte_representada_id" => $parteRepresentada,
                    "representante" => true
                ]);
                Compareciente::create(["parte_id" => $representante->id, "audiencia_id" => $audiencia->id, "presentado" => true]);

                $solicitante = $solicitud->partes()->where('tipo_parte_id',1)->first();
                $citado = $solicitud->partes()->where('tipo_parte_id',2)->first();
                $manifestaciones = $this->obtenerManifestaciones();
                $manifestaciones = Arr::prepend($manifestaciones,"true");
                $manifestaciones = Arr::prepend($manifestaciones,"1");
                $manifestaciones[] = "1";
                $row = 1;
                foreach($manifestaciones as $manifestacion){
                    EtapaResolucionAudiencia::create([
                        "etapa_resolucion_id" => $row,
                        "audiencia_id" => $audiencia->id,
                        "evidencia" => $manifestacion
                    ]);
                    $row++;
                }
                ResolucionPartes::create([
                    "audiencia_id" => $audiencia->id,
                    "parte_solicitante_id" => $solicitante->id,
                    "parte_solicitada_id" => $citado->id,
                    "terminacion_bilateral_id" => 3
                ]);
                $dato_laboral = $citado->dato_laboral->first();
                $datos_laborales_cadena = $this->obtenerDatosLaborales();
                if($dato_laboral){
                    $dato_laboral->update(
                        ['horario_laboral'=>$datos_laborales_cadena[0],
                        "horario_comida"=>$datos_laborales_cadena[1],
                        "comida_dentro"=>$datos_laborales_cadena[2],
                        "dias_descanso"=>$datos_laborales_cadena[3],
                        "dias_vacaciones"=>$datos_laborales_cadena[4],
                        "dias_aguinaldo"=>$datos_laborales_cadena[5],
                        "prestaciones_adicionales"=>$datos_laborales_cadena[6]]
                    );
                }

                $conceptos = $this->obtenerConceptosPorCurp($curp);

                $audiencia_parte = $citado->audienciaParte->first();
                $montoTotal = 0;
                foreach($array_conceptos as $key => $concepto){
                    if(!empty($concepto)){
                        $resolucion_parte = ResolucionParteConcepto::create([
                            "resolucion_partes_id" => null, //$resolucionParte->id,
                            "audiencia_parte_id" => $audiencia_parte->id,
                            "concepto_pago_resoluciones_id" => $concepto,//$concepto["concepto_pago_resoluciones_id"],
                            "dias" => null,//intval($concepto["dias"]),
                            "monto" => $conceptos[$key],//$concepto["monto"],
                            "otro" => ""
                        ]);
                        $montoTotal += floatval($concepto);
                    }
                }
                ResolucionPagoDiferido::create([
                    "audiencia_id" => $audiencia->id,
                    "solicitante_id" => $solicitante->id,
                    "monto" => $montoTotal,
                    "fecha_pago" => Carbon::createFromFormat('Y-m-d h:i', $fecha_audiencia." 09:00")->format('Y-m-d h:i')
                ]);

                // Guardamos los comparecientes a la audiencia
                foreach($solicitud->partes as $parte){
                    if($parte->tipo_persona_id == 1){
                        Compareciente::create(["parte_id" => $parte->id, "audiencia_id" => $audiencia->id, "presentado" => true]);
                    }
                }

                DB::commit();
                return array("exito" => true,"audiencia_id" => $audiencia->id);
            }else{
                return array("exito" => false,"audiencia_id" => $solicitud->expediente->audiencia()->first()->id);
            }
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('En script:' . $e->getFile() . " En línea: " . $e->getLine() .
                    " Se emitió el siguiente mensaje: " . $e->getMessage() .
                    " Con código: " . $e->getCode() . " La traza es: " . $e->getTraceAsString());
            return array("exito" => false,"audiencia_id" => null);
        }
    }

    /**
     * Regresa un arreglo con los ID de los conceptos leyendo del archivo excel la hoja "CONCEPTOS" el rengón
     * de encabezados
     * @return array
     */
    public function obtenerConceptos() {
        $nombreArchivo = $this->nombreArchivo;
        $workbook = SpreadsheetParser::open($nombreArchivo, 'xlsx');
        $arreglo_conceptos = [];
        foreach ($workbook->createRowIterator($workbook->getWorksheetIndex('CONCEPTOS')) as $rowIndex => $conceptos) {
            if ($rowIndex == 1 && !empty($conceptos)) {
                foreach ($conceptos as $key => $concepto) {
                    if($key == 0) continue;
                    $concepto_pago = ConceptoPagoResolucion::where('nombre', $concepto)->first();
                    if ($concepto_pago) {
                        $arreglo_conceptos[] = $concepto_pago->id;
                    }
                    else {
                        $this->error("No se encontro el concepto" . $concepto);
                        return [];
                    }
                }
            }
        }
        return $arreglo_conceptos;
    }

    /**
     * Retorna un arreglo de CURPS que extrae del archivo excel de la hoja "CONCEPTOS"
     * @return array
     */
    function obtenerCurp() {
        $nombreArchivo = $this->nombreArchivo;
        $workbook = SpreadsheetParser::open($nombreArchivo, 'xlsx');
        $curp = [];
        foreach ($workbook->createRowIterator($workbook->getWorksheetIndex('CONCEPTOS')) as $rowIndex => $values) {
            if ($this->curpValida($values[0])) {
                $curp[] = $values[0];
            }
        }
        return $curp;
    }

    /**
     * Valida las CURP
     * @param string $str CURP
     * @return false|int
     */
    function curpValida($str) {
        $pattern = '/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/';
        return preg_match($pattern, $str);
    }

    /**
     * Regresa un arreglo con los datos del conciliador que se extraen de la hoja "DATOS CONCILIADOR"
     * @return array
     */
    public function obtenerDatosConciliador(){
        $nombreArchivo = $this->nombreArchivo;
        $workbook = SpreadsheetParser::open($nombreArchivo, 'xlsx');
        $conciliador = null;
        foreach ($workbook->createRowIterator($workbook->getWorksheetIndex('DATOS CONCILIADOR')) as $rowIndex => $values) {
            if($rowIndex == 2){
                $conciliador = \App\Persona::where('nombre', 'ilike', $values[0]);
                if(isset($values[1])) $conciliador->where("primer_apellido", 'ilike', $values[1]);
                if(isset($values[2])) $conciliador->where("segundo_apellido", 'ilike', $values[2]);
                $conc = $conciliador->first();
                if($conc){
                    $conciliador = $conc->conciliador;
                }
                break;
            }
        }
        return $conciliador;
    }

    /**
     * Regresa un arreglo con los datos de las fechas involucradas en la audiencia como fecha_audiencia, hora_inicio_audiencia,
     * hora_fin_audiencia, fecha_resolucion
     *
     * @return array
     */
    private function getDatosFechasAudiencia(): array {
        $nombreArchivo = $this->nombreArchivo;
        $workbook = SpreadsheetParser::open($nombreArchivo, 'xlsx');
        $fecha_audiencia = '';
        $hora_inicio_audiencia = '';
        $hora_fin_audiencia = '';
        $fecha_resolucion = '';

        try {
            foreach ($workbook->createRowIterator($workbook->getWorksheetIndex('FECHAS AUDIENCIA')) as $rowIndex =>
                     $values) {
                if ($rowIndex == 2) {

                    $fecha_audiencia = $values[0];
                    if (!is_a($fecha_audiencia, \DateTime::class)) {
                        $fecha_audiencia = Carbon::createFromFormat('d/m/Y', $fecha_audiencia);
                    }
                    $fecha_audiencia = $fecha_audiencia->format("Y-m-d");

                    $hora_inicio_audiencia = $values[1];
                    if (!is_a($hora_inicio_audiencia, \DateTime::class)) {
                        $hora_inicio_audiencia = Carbon::createFromFormat('H:i', $hora_inicio_audiencia);
                    }
                    $hora_inicio_audiencia = $hora_inicio_audiencia->format("H:i");

                    $hora_fin_audiencia = $values[2];
                    if (!is_a($hora_fin_audiencia, \DateTime::class)) {
                        $hora_fin_audiencia = Carbon::createFromFormat('d/m/Y H:i', $hora_fin_audiencia);
                    }
                    $hora_fin_audiencia = $hora_fin_audiencia->format("Y-m-d H:i:00");

                    $fecha_resolucion = $values[3];
                    if (!is_a($fecha_resolucion, \DateTime::class)) {
                        $fecha_resolucion = Carbon::createFromFormat('d/m/Y H:i', $fecha_resolucion);
                    }
                    $fecha_resolucion = $fecha_resolucion->format("Y-m-d H:i:00");
                    break;
                }
            }
        }
        catch (Exception $e){

        }
        return array($fecha_audiencia, $hora_inicio_audiencia, $hora_fin_audiencia, $fecha_resolucion);
    }

    /**
     * Regresa en un arreglo los datos del representante que se extraen del archivo xlsx de la hoja DATOS REPRESENTANTE
     * @return array
     */
    private function obtenerDatosRepresentante(): array {

        $representante_data = [];
        $nombreArchivo = $this->nombreArchivo;
        $workbook = SpreadsheetParser::open($nombreArchivo, 'xlsx');
        foreach ($workbook->createRowIterator($workbook->getWorksheetIndex('DATOS REPRESENTANTE')) as $rowIndex => $values)
        {
            if ($rowIndex == 2) {
                $representante_data = $values;

                if (!is_a($values[3], \DateTime::class)) {
                    $values[3] = Carbon::createFromFormat('d/m/Y', $values[3]);
                }
                $representante_data[3] = $values[3]->format("Y-m-d");

                if (!is_a($values[5], \DateTime::class)) {
                    $values[5] = Carbon::createFromFormat('d/m/Y', $values[5]);
                }
                $representante_data[5] = $values[5]->format("Y-m-d");
                break;
            }
        }
        return $representante_data;
    }

    /**
     * Obtiene las manifestaciones del archivo xlsx de la hoja "MANIFESTACIONES"
     * @return array
     */
    private function obtenerManifestaciones(): array {
        $manifestaciones = [];
        $nombreArchivo = $this->nombreArchivo;
        $workbook = SpreadsheetParser::open($nombreArchivo, 'xlsx');
        foreach ($workbook->createRowIterator($workbook->getWorksheetIndex('MANIFESTACIONES')) as $rowIndex => $values)
        {
            if ($rowIndex == 2) {
                $manifestaciones = $values;
                break;
            }
        }
        return $manifestaciones;
    }

    /**
     * Obtiene los datos laborales del archivo xlsx de la hoja "DATOS LABORALES"
     * @return array
     */
    private function obtenerDatosLaborales(): array {
        $datos_laborales_cadena = [];
        $nombreArchivo = $this->nombreArchivo;
        $workbook = SpreadsheetParser::open($nombreArchivo, 'xlsx');
        foreach ($workbook->createRowIterator($workbook->getWorksheetIndex('DATOS LABORALES')) as $rowIndex => $values)
        {
            if ($rowIndex == 2) {
                $datos_laborales_cadena = $values;
                $datos_laborales_cadena[2] = (strtoupper($values[2]) == 'SI');
                break;
            }
        }
        return $datos_laborales_cadena;
    }

    /**
     * Obtiene los conceptos por cada CURP del archivo xlsx de la hoja "CONCEPTOS"
     * @param string $curp La CURP llave de la fila.
     * @return array
     */
    private function obtenerConceptosPorCurp($curp): array {

        $nombreArchivo = $this->nombreArchivo;
        $workbook = SpreadsheetParser::open($nombreArchivo, 'xlsx');
        $conceptos = [];
        foreach ($workbook->createRowIterator($workbook->getWorksheetIndex('CONCEPTOS')) as $rowIndex => $values)
        {
            if($values[0] == $curp) {
                foreach ($values as $key => $concepto) {
                    if ($key > 0) {
                        $repla = array("$", ",", "(", ")", "-");
                        $conceptos[] = trim(str_replace($repla, "", $concepto));
                    }
                }
                break;
            }
        }
        return $conceptos;
    }
}
