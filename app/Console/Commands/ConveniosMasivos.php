<?php

namespace App\Console\Commands;

use App\Audiencia;
use App\AudienciaParte;
use App\Centro;
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

class ConveniosMasivos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convenioMasivo {nombre : Path al archivo xlsx que trae los datos de los convenios} {--fecha-audiencia= : Fecha de la audiencia en formato Y-m-d} {--hora-inicio-audiencia= : Hora de inicio de la audiencia del conficto en formato HH:mm:ss} {--hora-fin-audiencia= : hora fin de la audiencia en formato Y-m-d HH:mm:ss} {--fecha-resolucion-audiencia= : Fecha del conficto en formato Y-m-d HH:mm:ss} {--cadena-representante= : Cadena separada por comas de los datos del representante: "Nombre","Primer Apellido","Segundo apellido","fecha de nacimiento","Genero (H-M)","fecha de instrumento notarial","CURP"} {--cadena-manifestaciones= : Cadena separada por comas de los datos de las manifestaciones: "Primera Manifestacion","Propuesta de convenio","Segunda manifestacion"} {--cadena-dato-laboral= : Cadena separada por comas de los datos laborales extra del trabajador "horario_laboral","horario_comida","comida_dentro","dias_descanso","dias_vacaciones","dias_aguinaldo","prestaciones_adicionales"} {--cadena-concepto-resolucion= : Cadena separada por comas de los conceptos de resolucion separados por coma, segun catalogo ConceptoPagoResolucion}';

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
        $archivo = __DIR__."/../../../".$nombreArchivo;
        $existe = file_exists($archivo);
        if(!empty($nombreArchivo) && $existe){
            //            Obtenemos el documento que contiene las CURP
            $arreglo = $this->obtenerCurp($archivo);

            $array_conceptos = $this->obtenerConceptos($archivo);
            if(empty($array_conceptos)){
                $error = "Los conceptos no esta correctamente configurados";
                $this->error($error);
                fputs($failedConv, $error."\n");
                return;
            }
            //            Recorremos todas las curp
            $array = [];
            foreach ($arreglo as $key => $curp) {
            //Localizamos la Parte para obtener la solicitud
                $parte = Parte::whereCurp($curp)->first();
            //Aquí comienza el proceso de confirmación y generación del expediente
                if ($parte != null) {
                    $solicitud = $parte->solicitud;
                    if($solicitud->expediente == null){
                        $registro = $this->ConfirmarSolicitudMultiple($solicitud,$curp,$array_conceptos);
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


    private function ConfirmarSolicitudMultiple(Solicitud $solicitud,$curp,$array_conceptos) {
        try {
            DB::beginTransaction();
            //obtenemos los folios 
            $ContadorController = new ContadorController();
            $folioC = $ContadorController->getContador(1, $solicitud->centro->id);
            $folioAudiencia = $ContadorController->getContador(3, 15);
            
//            Colocamos los parametros en variables
            $tipoParte = \App\TipoParte::whereNombre("SOLICITANTE")->first();
            $fecha_audiencia = $this->option('fecha-audiencia');
            $hora_inicio_audiencia = $this->option('hora-inicio-audiencia');
            $hora_fin_audiencia = $this->option('hora-fin-audiencia');
            $fecha_resolucion = $this->option('fecha-resolucion-audiencia');
            $resolucion_id = 1;
            
////        Obtenemos la sala virtual
            $sala = Sala::where("centro_id", $solicitud->centro_id)->where("virtual", true)->first();
            if ($sala == null) {
                DB::rollBack();
                return array("exito" => false,"audiencia_id" => null);
            }
            $sala_id = $sala->id;
            
//            Obtenemos un conciliador central
            $oficinaCentral = Centro::whereAbreviatura("OCCFCRL")->first();
            $conciliador = \App\Persona::whereNombre("ANA KAREN")->where("primer_apellido","NAVA HERNÁNDEZ")->first()->conciliador;
            if ($conciliador == null) {
                DB::rollBack();
                return array("exito" => false,"audiencia_id" => null);
            }
//            
////            Validamos si ya hay un expediente de la solicitud
            if ($solicitud->expediente == null) {
//            Creamos la estructura del folio
                $edo_folio = $solicitud->centro->abreviatura;
                $folio = $edo_folio . "/CJ/I/" . $folioC->anio . "/" . sprintf("%06d", $folioC->contador);
                //Creamos el expediente de la solicitud
                $expediente = Expediente::create(["solicitud_id" => $solicitud->id, "folio" => $folio, "anio" => $folioC->anio, "consecutivo" => $folioC->contador]);
//                Indicamos que el solicitante esta ratificando
                foreach ($solicitud->partes as $key => $parte) {
                    if ($tipoParte->id == $parte->tipo_parte_id) {
                        $parte->update(["ratifico" => true]);
                    }
                }
//                Modificamos la solicitud para indicar que ya se ratifico
                $solicitud->update(["estatus_solicitud_id" => 3, "url_virtual" => null, "ratificada" => true, "fecha_ratificacion" => now(), "inmediata" => true]);

//            Hacemos el registro de la audiencia
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
//                Registramos las partes a la audiencia
                foreach ($solicitud->partes as $parte) {
                    AudienciaParte::create(["audiencia_id" => $audiencia->id, "parte_id" => $parte->id, "tipo_notificacion_id" => null]);
                    if ($parte->tipo_parte_id == 2) {
                        // generar citatorio de conciliacion
                        event(new GenerateDocumentResolution($audiencia->id, $solicitud->id, 14, 4, null, $parte->id));
                    }
                }
//                Eliminamos el acuse de la solicitud
                $acuse = Documento::where('documentable_type', 'App\Solicitud')->where('documentable_id', $solicitud->id)->where('clasificacion_archivo_id', 40)->first();
                if ($acuse != null) {
                    $acuse->delete();
                }
//                Creamos el nuevo acuse
                event(new GenerateDocumentResolution("", $solicitud->id, 40, 6));
                
                
//                Registramos el representante legal
                $parteRepresentada = null;
                foreach($solicitud->partes as $parte){
                    if($parte->tipo_parte_id == 1){
                        $parteRepresentada = $parte->id;
                    }
                }
                
                $representante_data = str_getcsv($this->option('cadena-representante'));
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
                $manifestaciones = str_getcsv($this->option('cadena-manifestaciones'));
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
                $datos_laborales_cadena = str_getcsv($this->option('cadena-dato-laboral'));
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
                //$resoluciones = str_getcsv($this->option('cadena-concepto-resolucion'));
                
                $nombreArchivo = $this->argument('nombre');
                $filename = __DIR__."/../../../".$nombreArchivo;
                $file = fopen($filename, "r");
                $conceptos = array();
                $row = 0;
                while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                    if( $data[0] == $curp){
                        foreach($data as $key=> $concepto){
                            if($key > 1){
                                $repla = array("$",",","(",")","-");
                                $conceptos[] = trim(str_replace($repla,"",$concepto));
                            }
                        }
                        break;
                    }
                    $row++;
                }
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

//              Guardamos los comparecientes a la audiencia
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
    function obtenerConceptos($nombreArchivo) {
        $arreglo_conceptos = [];
        $file = fopen($nombreArchivo, "r");
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                foreach($data as $key=> $concepto){
                    if($key > 1 && !empty($concepto)){
                        $concepto_pago = ConceptoPagoResolucion::where('nombre',$concepto)->first();
                        if($concepto_pago){
                            $arreglo_conceptos[] = $concepto_pago->id;
                        }else{
                            $this->error("No se encontro el concepto".$concepto);
                            return [];
                        }
                    }
                }
            break;
        }
        return $arreglo_conceptos;
    }
    function obtenerCurp($nombreArchivo) {
        $file = fopen($nombreArchivo, "r");
        $curp = array();
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            if ($this->curpValida($data[0])) {
                $curp[] = $data[0];
            }
        }
        return $curp;
    }

    function curpValida($str) {
        $pattern = '/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/';
        return preg_match($pattern, $str);
    }
}
