<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiciosCJFController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Devuelve estructura JSON del listado de conciliaciones no exitosas filtrados por fecha inicial y final
     * @return \Illuminate\Http\Response
     */
    public function listadoPorFechas()
    {
        $parametros = $this->request->getContent();
        try {
            $fechas = json_decode($parametros);

            //TODO: implementar la consulta y devolución de datos
            $this->sendResponse($fechas);

        }catch (\Exception $e){
            Log::error("[Error listadoPorFechas]:".$parametros, $e->getMessage());
            return $this->sendError("Se ha producido un error al procesar la consulta", [
                $e->getMessage()
            ] ,401);
        }
    }

    /**
     * Regresa estructura JSON del listado de expedientes no exitosos encontrados para la parte solicitante o
     * actora como le llaman en el CJF (ahora es actor en una demanda porque no se pudo conciliar)
     * @return \Illuminate\Http\Response
     */
    public function listadoPorNombreParteActora()
    {
        $parametros = $this->request->getContent();
        try {
            $estructuraNombre = json_decode($parametros);
            //TODO: implementar la consulta y devolución de datos mediante el service
            $resultado = [];

            return $this->sendResponse($resultado);

        }catch (\Exception $e){
            Log::error("[Error listadoPorFechas]:".$parametros, $e->getMessage());
            return $this->sendError("Se ha producido un error al procesar la consulta", [
                $e->getMessage()
            ] ,401);
        }

    }

    /**
     * Regresa estructura JSON del listado de expedientes no exitosos encontrados para la parte solicitada o
     * demandada como le llaman en el CJF (ahora es demandada porque no se pudo conciliar)
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function listadoPorNombreParteDemandada()
    {
        $parametros = $this->request->getContent();
        try {
            $estructuraNombre = json_decode($parametros);
            //TODO: implementar la consulta y devolución de datos mediante el service
            $resultado = [];

            return $this->sendResponse($resultado);

        }catch (\Exception $e){
            Log::error("[Error listadoPorNombreParteDemandada]:".$parametros, $e->getMessage());
            return $this->sendError("Se ha producido un error al procesar la consulta", [
                $e->getMessage()
            ] ,401);
        }
    }

    /**
     * Devuelve listado de conciliaciones no existosas relacionadas con la persona con RFC
     * @param $rfc
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function listadoPorRFC($rfc)
    {

        try {

            //TODO: implementar la consulta y devolución de datos mediante el service
            $resultado = [];

            return $this->sendResponse($resultado);

        }catch (\Exception $e){
            Log::error("[Error listadoPorRfc]:".$rfc, $e->getMessage());
            return $this->sendError("Se ha producido un error al procesar la consulta", [
                $e->getMessage()
            ] ,401);
        }
    }

    /**
     * Devuelve listado de conciliaciones no existosas relacionadas con la persona con CURP
     * @param $curp
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function listadoPorCURP($curp)
    {
        try {

            //TODO: implementar la consulta y devolución de datos mediante el service
            $resultado = [];

            return $this->sendResponse($resultado);

        }catch (\Exception $e){
            Log::error("[Error listadoPorRfc]:".$curp, $e->getMessage());
            return $this->sendError("Se ha producido un error al procesar la consulta", [
                $e->getMessage()
            ] ,401);
        }
    }

    /**
     * Devuelve todos los datos del proceso de conciliación relacionados.
     * @param $expediente
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function consultaExpediente($expediente)
    {
        try {

            //TODO: implementar la consulta y devolución de datos mediante el service
            $resultado = [];

            return $this->sendResponse($resultado);

        }catch (\Exception $e){
            Log::error("[Error listadoPorRfc]:".$expediente, $e->getMessage());
            return $this->sendError("Se ha producido un error al procesar la consulta", [
                $e->getMessage()
            ] ,401);
        }

    }
}
