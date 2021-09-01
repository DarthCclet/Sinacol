<?php

namespace App\Console\Commands;

use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Estado;
use App\Genero;
use App\GiroComercial;
use App\Jornada;
use App\Municipio;
use App\Nacionalidad;
use App\ObjetoSolicitud;
use App\Periodicidad;
use App\Services\ReportesService;
use App\TipoObjetoSolicitud;
use App\TipoVialidad;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Crea solicitudes masivas a partir de un archivo con datos de los citados y opciones de solicitante
 *
 * Class SolicitudMasiva
 * @package App\Console\Commands
 */
class SolicitudMasiva extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solicitudMasiva {nombre? : Path al archivo xlsx que trae los datos de los citados}
                                {--cadena-solicitante= : Cadena separada por comas de los datos del solicitante: "Tipo Persona","Nombre empresa","RFC","tel1","tel2","correo","Estado","tipo vialidad","vialidad","num_ext","num_int","asentamiento","municipio","codigo postal"}
                                {--fecha-conflicto= : Fecha del conficto en formato Y-m-d}
                                {--tipo-solicitud= : Tipo solicitud patrón inviduvual, trabajador individual, colectivo}
                                {--objeto-solicitud= : Objeto solicitud}
                                {--industria= : Giro industrial}
                                {--virtual= : Indica SI o NO si la solicitud es virtual o presencial}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para capturar masivamente solicitudes a partir de un archivo';


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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    //
    public function handle()
    {
        $nombreArchivo = $this->argument('nombre');
        DB::enableQueryLog();
        $archivo = __DIR__."/../../../".$nombreArchivo;
        $existe = file_exists($archivo);
        if(!empty($nombreArchivo) && $existe){

            $partesCitado = [];
            $workbook = SpreadsheetParser::open($archivo, 'xlsx');

            foreach ($workbook->createRowIterator(0) as $rowIndex => $values) {
                if($rowIndex < 5) continue;
                array_push($partesCitado,$values);
            }
            $client = new Client(['base_uri' => env('APP_URL')]);
            $savedSol = fopen(__DIR__."/../../../public/savedSol.txt", 'w');
            $failedSol = fopen(__DIR__."/../../../public/failedSol.txt", 'w');

            $tipo_objeto_solicitud = TipoObjetoSolicitud::where('nombre','ilike', '%'.$this->option('tipo-solicitud').'%')->first();
            if(!$tipo_objeto_solicitud) {
                $this->error("No existe el tipo de solicitud: ".$this->option('tipo-solicitud'));
                return;
            }

            $objeto_solicitud = ObjetoSolicitud::whereRaw("unaccent(nombre) ilike unaccent('".$this->option('objeto-solicitud')."')")
                ->where('tipo_objeto_solicitudes_id', $tipo_objeto_solicitud->id)
                ->first();
            if(!$objeto_solicitud){
                $this->error("No existe el objeto de la solicitud: ".$this->option('objeto-solicitud'));
                dd(ReportesService::debugSql());
                return;
            }
            $partesSolicitante = str_getcsv($this->option('cadena-solicitante'));

            $solicitudObj = [
                "fecha_conflicto" => $this->option('fecha-conflicto'),
                "giro_comercial"  => $this->option('industria'),
                "virtual"         => (strtolower($this->option('virtual')) === 'si'),
                "tipo_solicitud_id" => $tipo_objeto_solicitud->id
            ];

            $correctos = 0;
            $erroneos = 0;
            foreach ($partesCitado as $key => $value) {
                try{
                    $solicitud = self::getSolicitud($solicitudObj);
                    $objeto_solicitudObj = array(
                        "id" => null,
                        "objeto_solicitud_id" => $objeto_solicitud->id,
                        "activo" => "1"
                    );
                    $citado = self::getCitado($value);
                    $solicitante = self::getSolicitante($partesSolicitante);
                    $resp = $client->request('POST','api/solicitud',[
                    "json" => [
                        "solicitud"=>$solicitud,
                        "objeto_solicitudes" => [$objeto_solicitudObj],
                        "solicitados" => [$citado],
                        "solicitantes"=>[$solicitante]
                    ],
                    'verify' => false ,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . session('token'),
                    ],
                    ]);
                    $correctos++;
                    $this->info($resp->getBody());
                    fputs($savedSol, "Num. Correcto: ".$correctos." ".$resp->getBody()."\n");
                }catch(Exception $e){
                    Log::error('En script:'.$e->getFile()." En línea: ".$e->getLine().
                    " Se emitió el siguiente mensale: ". $e->getMessage().
                    " Con código: ".$e->getCode()." La traza es: ". $e->getTraceAsString());
                    $erroneos++;
                    $error = "Num. erroneo: ".$erroneos." Renglon: ".($key+1)." CURP: ".$value[0]." Error:".$e->getMessage()." LCOD: ".$e->getLine();
                    $this->error($error);
                    fputs($failedSol, $error."\n");
                }


            }
        }else{
            $this->error("No se encontró el archivo");
        }
    }

    private function getSolicitud($solicitud){
        $giro = GiroComercial::where('nombre','ilike',$solicitud["giro_comercial"])->first();
        return array(
            "id" => null,
            "observaciones" => null,
            "solicita_excepcion" => "false",
            "fecha_conflicto" => $solicitud["fecha_conflicto"],
            "tipo_solicitud_id" => $solicitud["tipo_solicitud_id"],
            "giro_comercial_id" => $giro->id,
            "virtual" => $solicitud["virtual"],
            "recibo_oficial" => "false",
            "recibo_pago" => "false",
        );
    }

    private function getCitado($citado){
        $periodicidad = Periodicidad::whereRaw("nombre ilike '%".strtoupper($citado[24])."%'")->first();

        $jornada = Jornada::whereRaw("nombre ilike '%".strtoupper($citado[29])."%'")->first();
        $dato_laboral = array(
            "id" => null,
            "ocupacion_id" => null,
            "puesto" => $citado[21],
            "nss" => $citado[20],
            "no_issste" => null,
            "remuneracion" => $citado[23],
            "periodicidad_id" => $periodicidad->id,
            "labora_actualmente" => $citado[26] == "SI" ? true : false,
            "fecha_ingreso" =>  Carbon::parse($citado[27])->format('Y-m-d'),
            "fecha_salida" => Carbon::parse($citado[28])->format('Y-m-d'),
            "jornada_id" => $jornada->id,
            "horas_semanales" => $citado[25],
            "resolucion" => "false",
        );
        $estado = Estado::whereRaw("unaccent(nombre) ilike unaccent('".$citado[12]."')")->first();
        $tipo_vialidad = TipoVialidad::whereRaw("unaccent(nombre) ilike unaccent('$citado[13]')")->first();
        $municipio = Municipio::whereRaw("unaccent(municipio) ilike unaccent('".$citado[18]."')")->first();
        $domicilioCitado = array(
            "id" => null,
            "num_ext" => $citado[15],
            "num_int" => $citado[16],
            "asentamiento" => $citado[17],
            "municipio" => $municipio->municipio,
            "cp" => substr($citado[19],0,5),
            "entre_calle1" => null,
            "entre_calle2" => null,
            "referencias" => null,
            "tipo_vialidad_id" => $tipo_vialidad->id,
            "tipo_vialidad" => $tipo_vialidad->nombre,
            "vialidad" => $citado[14],
            "tipo_asentamiento_id" => null,
            "tipo_asentamiento" => null,
            "estado_id" => $estado->id,
            "estado" => $estado->nombre,
            "latitud" => null,
            "longitud" => null,
            "georeferenciable" => "false",
            "activo" => "1",
        );
        $contactosCitado = [];
        if(!empty($citado[9])){
            array_push($contactosCitado,
                [
                    "id" => null,
                    "activo" => "1",
                    "contacto" => $citado[9],
                    "tipo_contacto_id" => "1"
                ]
            );
        }
        if(!empty($citado[10])){
            array_push($contactosCitado,[
                "id" => null,
                "activo" => "1",
                "contacto" => $citado[10],
                "tipo_contacto_id" => "2"
            ]);
        }
        if(!empty($citado[11])){
            array_push($contactosCitado,[
                "id" => null,
                "activo" => "1",
                "contacto" => $citado[11],
                "tipo_contacto_id" => "3"
            ]);
        }

        $abreviaturaGenero = $citado[6][0];
        $genero = Genero::where('abreviatura','ilike',$abreviaturaGenero)->first();
        $nacionalidad = Nacionalidad::where('nombre','like','%'.$citado[7].'%')->first();
        $estado_nacimiento = Estado::where('nombre','like','%'.$citado[8].'%')->first();
        $citadoObj = array(
            "id" => null,
            "nombre" => $citado[1],
            "primer_apellido" => $citado[2],
            "segundo_apellido" => $citado[3],
            "curp" => $citado[0],
            "edad" => null,
            "genero_id" => $genero->id,
            "nacionalidad_id" => $nacionalidad->id,
            "lengua_indigena_id" => null,
            "tipo_persona_id" => "1",
            "tipo_parte_id" => "2",
            "entidad_nacimiento_id" => $estado_nacimiento->id,
            "fecha_nacimiento" => Carbon::parse($citado[4])->format('Y-m-d'),
            "rfc" => $citado[5],
            "activo" => "1",
            "dato_laboral" =>  $dato_laboral,
            "domicilios"=>[
                $domicilioCitado
            ],
            "contactos" => $contactosCitado
        );
        return $citadoObj;
    }

    private function getSolicitante($solicitante){
        $contactosSolicitante = [];
        if(!empty($solicitante[3])){
            array_push($contactosSolicitante,
                [
                    "id" => null,
                    "activo" => "1",
                    "contacto" => $solicitante[3],
                    "tipo_contacto_id" => "1"
                ]
            );
        }
        if(!empty($solicitante[4])){
            array_push($contactosSolicitante,[
                "id" => null,
                "activo" => "1",
                "contacto" => $solicitante[4],
                "tipo_contacto_id" => "2"
            ]);
        }
        if(!empty($solicitante[5])){
            array_push($contactosSolicitante,[
                "id" => null,
                "activo" => "1",
                "contacto" => $solicitante[5],
                "tipo_contacto_id" => "3"
            ]);
        }
        $estado = Estado::where('nombre','like','%'.$solicitante[6].'%')->first();
        $tipo_vialidad = TipoVialidad::where('nombre','like','%'.$solicitante[7].'%')->first();
        $municipio = Municipio::where('municipio','like','%'.$solicitante[12].'%')->first();
        $domSolicitante = array(
            "id" => null,
            "num_ext" => $solicitante[9],
            "num_int" => $solicitante[10],
            "asentamiento" => $solicitante[11],
            "municipio" => $municipio->municipio,
            "cp" => $solicitante[13],
            "entre_calle1" => null,
            "entre_calle2" => null,
            "referencias" => null,
            "tipo_vialidad_id" => $tipo_vialidad->id,
            "tipo_vialidad" => $tipo_vialidad->nombre,
            "vialidad" => $solicitante[8],
            "tipo_asentamiento_id" => null,
            "tipo_asentamiento" => null,
            "estado_id" => $estado->id,
            "estado" => $estado->nombre,
            "latitud" => null,
            "longitud" => null,
            "georeferenciable" => "false",
            "activo" => "1",
        );
        //Estado $solicitante[6]
        $solicitante = array(
            "id" => null,
            "nombre_comercial" => $solicitante[1],
            "tipo_persona_id" => "2",
            "tipo_parte_id" => "1",
            "activo" => "1",
            "rfc" => $solicitante[2],
            "domicilios" => [
                $domSolicitante
            ],
            "contactos" => $contactosSolicitante

        );
        return $solicitante;
    }
}
