<?php

namespace App\Http\Controllers;

use App\Services\ConsultaConciliacionesPorExpediente;
use App\Services\ConsultaConciliacionesPorNombre;
use App\Services\ConsultaConciliacionesPorRangoFechas;
use App\Services\ConsultaConciliacionesPorCurp;
use App\Services\ConsultaConciliacionesPorRfc;
use App\Http\Controllers\ContadorController;
use App\TipoParte;
use App\TipoPersona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Parte;
use App\Audiencia;
use App\Solicitud;

class ServiciosCJFController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Devuelve estructura JSON del listado de conciliaciones no exitosas filtrados por fecha inicial y final
     * @param ConsultaConciliacionesPorRangoFechas $consulta
     * @return \Illuminate\Http\Response
     */
    public function listadoPorFechas(ConsultaConciliacionesPorRangoFechas $consulta)
    {
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(4,null);
        $parametros = $this->request->getContent();
        try {
            $fechas = $consulta->validaEstructuraParametros($parametros);
            $fecha_inicial = $consulta->validaFechas($fechas->fechaInicio);
            $fecha_final = $consulta->validaFechas($fechas->fechaFin);
            $fechas = $consulta->consulta($fecha_inicial, $fecha_final);
            $acuse = [
                'codigo_retorno' => 1,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'EXITO'
            ];
            return response()->json(array_merge($fechas, $acuse), 200);
        }catch (\Exception $e){
            Log::error("[Error listadoPorFechas]:");
            $acuse = [
                'codigo_retorno' => $e->getCode(),
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'ERROR: '.$e->getMessage()
            ];
            return response()->json(array_merge([], $acuse), 400);
        }
    }

    /**
     * Regresa estructura JSON del listado de expedientes no exitosos encontrados para la parte solicitante o
     * actora como le llaman en el CJF (ahora es actor en una demanda porque no se pudo conciliar)
     * @param ConsultaConciliacionesPorNombre $consulta
     * @return \Illuminate\Http\Response
     */
    public function listadoPorNombreParteActora(ConsultaConciliacionesPorNombre $consulta)
    {
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(4,null);
        $tipoSolicitante = TipoParte::where('nombre','ilike','SOLICITANTE')->first();
        $parametros = $this->request->getContent();
        try {
            $estructuraNombre = $consulta->validaEstructuraParametros($parametros);
            $tipoPersona = TipoPersona::where('nombre', 'ilike', $estructuraNombre->caracter_persona)->first();
            $resultado = [];
            $resultado = $consulta->consulta(
                $estructuraNombre->nombre,
                $estructuraNombre->primer_apellido,
                $estructuraNombre->segundo_apellido,
                $tipoPersona->id,
                $tipoSolicitante->id
                );
            $acuse = [
                'codigo_retorno' => 1,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'EXITO'
            ];
            return response()->json(array_merge($resultado, $acuse), 200);
        }
        catch (\Exception $e){
            Log::error("[Error listadoPorNombreParteActora]:".$parametros. $e->getMessage());
            $acuse = [
                'codigo_retorno' => $e->getCode(),
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'ERROR: '.$e->getMessage()
            ];
            return response()->json(array_merge([], $acuse), 400);
        }
    }

    /**
     * Regresa estructura JSON del listado de expedientes no exitosos encontrados para la parte solicitada o
     * demandada como le llaman en el CJF (ahora es demandada porque no se pudo conciliar)
     * @param ConsultaConciliacionesPorNombre $consulta
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function listadoPorNombreParteDemandada(ConsultaConciliacionesPorNombre $consulta)
    {
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(4,null);
        $tipoSolicitado = TipoParte::where('nombre','ilike','SOLICITADO')->first();
        $parametros = $this->request->getContent();
        try {
            $estructuraNombre = $consulta->validaEstructuraParametros($parametros);
            $tipoPersona = TipoPersona::where('nombre', 'ilike', $estructuraNombre->caracter_persona)->first();
            $resultado = [];
            $resultado = $consulta->consulta(
                $estructuraNombre->nombre,
                $estructuraNombre->primer_apellido,
                $estructuraNombre->segundo_apellido,
                $tipoPersona->id,
                $tipoSolicitado->id
            );
            $acuse = [
                'codigo_retorno' => 1,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'EXITO'
            ];
            return response()->json(array_merge($resultado, $acuse), 200);
        }
        catch (\Exception $e){
            Log::error("[Error listadoPorNombreParteActora]:".$parametros. $e->getMessage());
            $acuse = [
                'codigo_retorno' => $e->getCode(),
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'ERROR: '.$e->getMessage()
            ];
            return response()->json(array_merge([], $acuse), 400);
        }
    }

    /**
     * Devuelve listado de conciliaciones no existosas relacionadas con la persona con RFC
     * @param $rfc
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function listadoPorRFC($rfc)
    {
        //Obtenemos el contador
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(4,null);
        try {

            $ConsultaConciliacionesPorRFC = new ConsultaConciliacionesPorRfc();
            $solicitudes = $ConsultaConciliacionesPorRFC->consulta($rfc);

            //TODO: implementar la consulta y devolución de datos mediante el service
            $acuse = [
                'codigo_retorno' => 1,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'EXITO'
            ];
            return response()->json(array_merge($solicitudes, $acuse), 200);

        }catch (\Exception $e){
            Log::error("[Error listadoPorCURP]:");
            $acuse = [
                'codigo_retorno' => 0,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'ERROR: '.$e->getMessage()
            ];
            return response()->json(array_merge([], $acuse), 400);
        }
    }

    /**
     * Devuelve listado de conciliaciones no existosas relacionadas con la persona con CURP
     * @param $curp
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function listadoPorCURP($curp)
    {
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(4,null);
        try {

            $ConsultaConciliacionesPorCURP = new ConsultaConciliacionesPorCurp();
            $solicitudes = $ConsultaConciliacionesPorCURP->consulta($curp);

            //TODO: implementar la consulta y devolución de datos mediante el service
            $acuse = [
                'codigo_retorno' => 1,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'EXITO'
            ];
            return response()->json(array_merge($solicitudes, $acuse), 200);

        }catch (\Exception $e){
            Log::error("[Error listadoPorCURP]:");
            $acuse = [
                'codigo_retorno' => 0,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'ERROR: '.$e->getMessage()
            ];
            return response()->json(array_merge([], $acuse), 400);
        }
    }

    /**
     * Devuelve todos los datos del proceso de conciliación relacionados.
     * @param ConsultaConciliacionesPorExpediente $consulta
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function consultaExpediente(ConsultaConciliacionesPorExpediente $consulta)
    {
        $ContadorController = new ContadorController();
        $folio = $ContadorController->getContador(4,null);
        try {
            $parametros = $this->request->getContent();
            $expediente = $consulta->validaEstructuraParametros($parametros);
            $solicitudes = $consulta->consulta($expediente->expediente, 3);

            $acuse = [
                'codigo_retorno' => 1,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'EXITO'
            ];
            return response()->json(array_merge($solicitudes, $acuse), 200);
//            return $this->sendResponse($resultado,'Resultados de busqueda del CURP: '.$curp);

        }catch (\Exception $e){
            Log::error("[Error listadoPorCURP]:");
            $acuse = [
                'codigo_retorno' => 0,
                'fecha_recepcion' => "/Date(".Carbon::now()->timestamp.Carbon::now()->milli. str_replace(":","",Carbon::now('America/Mexico_City')->format("P")).")/",
                'folio_confirmacion' => sprintf("%06d", $folio->contador),
                'mensaje' => 'ERROR: '.$e->getMessage()
            ];
            return response()->json(array_merge([], $acuse), 400);
        }

    }
}
