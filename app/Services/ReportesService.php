<?php


namespace App\Services;


use App\Audiencia;
use App\AudienciaParte;
use App\Filters\AudienciaFilter;
use App\Filters\AudienciaParteFilter;
use App\Filters\ResolucionParteConceptoFilter;
use App\Filters\SolicitudFilter;
use App\ResolucionParteConcepto;
use App\Solicitud;
use Illuminate\Support\Facades\DB;

class ReportesService
{
    /**
     * ID de solicitante en catálogo tipo_partes
     */
    const SOLICITANTE_ID = 1;

    /**
     * ID de tipo de solicitud individual en catálogo de tipos_solicitudes
     */
    const SOLICITUD_INDIVIDUAL = 1;

    /**
     * ID de tipo de solicitud patronal individual en catálogo de tipos_solicitudes
     */
    const SOLICITUD_PATRONAL_INDIVIDUAL = 2;

    /**
     * ID de error de captura en catálogo tipo_incidencia_solicitudes
     */
    const ERROR_DE_CAPTURA = 1;

    /**
     * ID de solicitud deuplicada en catálogo de tipo_incidencias_solicitudes
     */
    const SOLICITUD_DUPLICADA = 2;

    /**
     * ID de otros en catálogo de tipo_incidencias_solicitudes
     */
    const OTRA_INCIDENCIA = 5;

    /**
     * ID de la incompetencia de tipo_incidencias_solicitudes
     */
    const INCOMPETENCIA_EN_RATIFICACION = 4;

    /**
     * ID de la incompetencia en audiencia en catálogo de tipos de terminaciones de audiencias
     */
    const INCOMPETENCIA_EN_AUDIENCIA = 6;

    /**
     * ID de tipo de parte
     */
    const CITADO = 2;

    /**
     * ID de tipo de citatorios
     */
    const CITATORIO_POR_SOLICITANTE = 1;
    const CITATORIO_POR_NOTIFICADOR = 2;
    const CITATORIO_POR_NOTIFICADOR_ACOMPANIADO = 3;
    const CITATORIO_POR_EBUZON = 4;

    /**
     * ID de archivado en catálogo de resoluciones, se utiliza en archivado por falta de interés de la audiencia
     */
    const ARCHIVADO = 4;

    /**
     * Sobre las solicitudes presentadas
     * @param $request
     * @return mixed
     */
    public function solicitudesPresentadas($request)
    {
        $q = (new SolicitudFilter(Solicitud::query(), $request))
            ->searchWith(Solicitud::class)
            ->filter(false);


        //Las solicitudes presentadas se evaluan por fecha de recepcion
        if($request->get('fecha_inicial')){
            $q->whereRaw('fecha_recepcion::date >= ?', $request->get('fecha_inicial'));
        }
        if($request->get('fecha_final')){
            $q->whereRaw('fecha_recepcion::date <= ?', $request->get('fecha_final'));
        }

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('centros','solicitudes.centro_id','=','centros.id');

        //Seleccionamos la abreviatura del nombre y su cuenta
        $q->select('centros.abreviatura', DB::raw('count(*)'))->groupBy('centros.abreviatura');

        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        //Dejamos fuera los no consultables
        $this->noReportables($q);

        $res = $q->get()->sortBy('abreviatura')->pluck('count', 'abreviatura');
        return $res;
    }

    /**
     * Sobre las solicitudes confirmadas
     * @param $request
     * @return mixed
     */
    public function solicitudesConfirmadas($request)
    {
        $q = (new SolicitudFilter(Solicitud::query(), $request))
            ->searchWith(Solicitud::class)
            ->filter(false);

        //Las solicitudes confirmadas se evaluan por fecha de ratificacion
        if($request->get('fecha_inicial')){
            $q->whereRaw('fecha_ratificacion::date >= ?', $request->get('fecha_inicial'));
        }
        if($request->get('fecha_final')){
            $q->whereRaw('fecha_ratificacion::date <= ?', $request->get('fecha_final'));
        }

        //Seleccionamos la abreviatura del nombre y su cuenta
        $q->select('centros.abreviatura', 'inmediata');

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('centros','solicitudes.centro_id','=','centros.id');

        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        //Se filtran las no reportables
        $this->noReportables($q);

        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura');
        $inmediata = $res->where('inmediata',true)->groupBy('abreviatura');
        $normal = $res->where('inmediata',false)->groupBy('abreviatura');

        return [$inmediata, $normal];
    }

    /**
     * Con respecto a los citatorios emitidos
     * @param $request
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection Devueve arreglos
     */
    public function citatoriosEmitidos($request)
    {
        //$q = AudienciaParte::with(['audiencia', 'audiencia.expediente', 'audiencia.expediente.solicitud', 'parte']);
        $q = (new AudienciaParteFilter(AudienciaParte::query(), $request))
            ->searchWith(Solicitud::class)
            ->filter(false);

        $q->with(['audiencia.expediente', 'audiencia.expediente.solicitud']);

        //Las solicitudes presentadas se evaluan por fecha de recepcion
        if($request->get('fecha_inicial')){
            $q->whereRaw('audiencias_partes.created_at::date >= ?', $request->get('fecha_inicial'));
        }
        if($request->get('fecha_final')){
            $q->whereRaw('audiencias_partes.created_at::date <= ?', $request->get('fecha_final'));
        }

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('audiencias','audiencias.id','=','audiencias_partes.audiencia_id');
        $q->join('expedientes','expedientes.id','=','audiencias.expediente_id');
        $q->join('solicitudes','solicitudes.id','=','expedientes.solicitud_id');
        $q->join('centros','solicitudes.centro_id','=','centros.id');
        $q->join('partes','partes.id', '=','audiencias_partes.parte_id');

        //Seleccionamos la abreviatura del nombre y su cuenta
        $q->select('centros.abreviatura', 'tipo_notificacion_id', 'audiencias.numero_audiencia' );
        //$q->select('centros.abreviatura', DB::raw('count(*)'))->groupBy('centros.abreviatura');


        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteAudienciaParte($request, $q);

        //Dejamos fuera los no consultables
        //$this->noReportables($q);

        //Sólo las de trabajador y patron individual
        $q->whereIn('solicitudes.tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $q->whereNull('audiencias.deleted_at');
        $q->whereNull('solicitudes.deleted_at');
        $q->whereNotNull('audiencias_partes.tipo_notificacion_id');

        $q->where('solicitudes.inmediata', false);
        $q->where('partes.tipo_parte_id', self::CITADO);

        $q->whereRaw('(audiencias_partes.created_at::date > solicitudes.fecha_ratificacion::date and audiencias_partes.tipo_notificacion_id = 1) = false');

        $res = $q->get()->sortBy('abreviatura');


        //Entrega Solicitante
        $entrega_solicitante = $res->where('tipo_notificacion_id', self::CITATORIO_POR_SOLICITANTE)
            ->groupBy('abreviatura')
            ->map(function ($en_centro, $centro) {
                return count($en_centro);
            })->toArray();

        $entrega_notificador = $res->where('tipo_notificacion_id', self::CITATORIO_POR_NOTIFICADOR)
            ->groupBy('abreviatura')
            ->map(function ($en_centro, $centro) {
                return count($en_centro);
            })->toArray();

        $entrega_notificador_cita = $res->where('tipo_notificacion_id', self::CITATORIO_POR_NOTIFICADOR_ACOMPANIADO)
            ->groupBy('abreviatura')
            ->map(function ($en_centro, $centro) {
                return count($en_centro);
            })->toArray();

        //En primera audiencia
        $citatorio_en_primera_audiencia = $res->where('numero_audiencia', 1)
            ->groupBy('abreviatura')
            ->map(function ($en_centro, $centro) {
                return count($en_centro);
            })->toArray();

        //En segunda audiencia
        $citatorio_en_segunda_audiencia = $res->where('numero_audiencia',2)
            ->groupBy('abreviatura')
            ->map(function ($en_centro, $centro) {
                return count($en_centro);
            })->toArray();

        //En tercera audiencia
        $citatorio_en_tercera_audiencia = $res->where('numero_audiencia',3)
            ->groupBy('abreviatura')
            ->map(function ($en_centro, $centro) {
                return count($en_centro);
            })->toArray();


        return compact(
            'entrega_solicitante',
            'entrega_notificador',
            'entrega_notificador_cita',
            'citatorio_en_primera_audiencia',
            'citatorio_en_segunda_audiencia',
            'citatorio_en_tercera_audiencia'
        );
    }


    public function incompetencias($request)
    {
        $en_ratificacion = $this->incompetenciasEnRatificacion($request);
        $en_audiencia = $this->incompetenciasEnAudiencia($request);
        return compact('en_ratificacion','en_audiencia');
    }

    /**
     * Con respecto a las incompetencias
     * @param $request
     * @return array
     */
    public function incompetenciasEnRatificacion($request)
    {
        $q = (new SolicitudFilter(Solicitud::query(), $request))
            ->searchWith(Solicitud::class)
            ->filter(false);

        //Las solicitudes confirmadas se evaluan por fecha de ratificacion
        if($request->get('fecha_inicial')){
            $q->whereRaw('fecha_recepcion::date >= ?', $request->get('fecha_inicial'));
        }
        if($request->get('fecha_final')){
            $q->whereRaw('fecha_recepcion::date <= ?', $request->get('fecha_final'));
        }

        //Seleccionamos la abreviatura del nombre y su cuenta
        $q->select('centros.abreviatura', DB::raw('count(*)'))->groupBy('centros.abreviatura');

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('centros','solicitudes.centro_id','=','centros.id');

        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        $q->where('tipo_incidencia_solicitud_id', self::INCOMPETENCIA_EN_RATIFICACION);

        //Se filtran las no reportables
        $this->noReportables($q);

        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('count','abreviatura');

        return $res->toArray();

    }

    public function incompetenciasEnAudiencia($request)
    {
        $q = (new AudienciaFilter(Audiencia::query(), $request))
            ->searchWith(Audiencia::class)
            ->filter(false);

        //Las solicitudes confirmadas se evaluan por fecha de ratificacion
        if($request->get('fecha_inicial')){
            $q->whereRaw('fecha_audiencia::date >= ?', $request->get('fecha_inicial'));
        }
        if($request->get('fecha_final')){
            $q->whereRaw('fecha_audiencia::date <= ?', $request->get('fecha_final'));
        }

        //Seleccionamos la abreviatura del nombre y su cuenta
        $q->select('centros.abreviatura', DB::raw('count(*)'))->groupBy('centros.abreviatura');

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('expedientes','expedientes.id','=','audiencias.expediente_id');
        $q->join('solicitudes','solicitudes.id','=','expedientes.solicitud_id');
        $q->join('centros','solicitudes.centro_id','=','centros.id');

        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        $q->where('tipo_terminacion_audiencia_id', self::INCOMPETENCIA_EN_AUDIENCIA);

        //Se filtran las no reportables
        $this->noReportables($q);

        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('count','abreviatura');

        return $res->toArray();
    }

    public function archivadoPorFaltaDeInteres($request)
    {
        $q = (new AudienciaFilter(Audiencia::query(), $request))
            ->searchWith(Audiencia::class)
            ->filter(false);

        //Las solicitudes confirmadas se evaluan por fecha de ratificacion
        if($request->get('fecha_inicial')){
            $q->whereRaw('fecha_audiencia::date >= ?', $request->get('fecha_inicial'));
        }
        if($request->get('fecha_final')){
            $q->whereRaw('fecha_audiencia::date <= ?', $request->get('fecha_final'));
        }

        //Seleccionamos la abreviatura del nombre y su cuenta
        $q->select('centros.abreviatura', DB::raw('count(*)'))->groupBy('centros.abreviatura');

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('expedientes','expedientes.id','=','audiencias.expediente_id');
        $q->join('solicitudes','solicitudes.id','=','expedientes.solicitud_id');
        $q->join('centros','solicitudes.centro_id','=','centros.id');

        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        $q->where('resolucion_id', self::ARCHIVADO);

        //Se filtran las no reportables
        $this->noReportables($q);

        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('count','abreviatura');

        return $res;
    }

    /**
     * Acerca de los convenios de conciliacion
     * @param $request
     */
    public function conveniosConciliacion($request)
    {
        $q = (new ResolucionParteConceptoFilter(ResolucionParteConcepto::query(), $request))
            ->searchWith(ResolucionParteConcepto::class)
            ->filter(false);

        //Las solicitudes confirmadas se evaluan por fecha de ratificacion
        if($request->get('fecha_inicial')){
            $q->whereRaw('fecha_audiencia::date >= ?', $request->get('fecha_inicial'));
        }
        if($request->get('fecha_final')){
            $q->whereRaw('fecha_audiencia::date <= ?', $request->get('fecha_final'));
        }

        //Seleccionamos la abreviatura del nombre y su cuenta
        //$q->select('centros.abreviatura', 'audiencias.id as audiencia_id', 'monto');
        $q->select('centros.abreviatura', DB::raw('sum(monto)'))->groupBy('centros.abreviatura');

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('resolucion_partes','resolucion_partes.id','=','resolucion_parte_conceptos.resolucion_partes_id');
        $q->join('audiencias','resolucion_partes.audiencia_id','=','audiencias.id');
        $q->join('expedientes','expedientes.id','=','audiencias.expediente_id');
        $q->join('solicitudes','solicitudes.id','=','expedientes.solicitud_id');
        $q->join('centros','solicitudes.centro_id','=','centros.id');

        //Se aplican filtros por características del solicitante
        //$this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        //Se filtran las no reportables
        $this->noReportables($q);

        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('sum','abreviatura');

        return $res;
    }

    /**
     * Excluye los no reportables
     * @param $q
     * @return mixed
     */
    private function noReportables($q)
    {
        //Eliminamos las incidencias duplicadas y errores de captura
        $q->whereRaw('tipo_incidencia_solicitud_id is distinct from ?', self::ERROR_DE_CAPTURA);
        $q->whereRaw('tipo_incidencia_solicitud_id is distinct from ?', self::SOLICITUD_DUPLICADA);
        $q->whereRaw('tipo_incidencia_solicitud_id is distinct from ?', self::OTRA_INCIDENCIA);
        return $q;
    }

    /**
     * Filtra por características de la parte solicitante
     * @param $request
     * @param $q
     * @return mixed
     */
    private function filtroPorCaracteristicasSolicitanteSolicitud($request, $q)
    {
        if($request->get('genero_id') || $request->get('edad_inicial') || $request->get('edad_final')){
            $genero_id = $request->get('genero_id');
            $edad_inicial = $request->get('edad_inicial');
            $edad_final = $request->get('edad_final');
            $q->with(['expediente','expediente.audiencia','expediente.audiencia.audienciaParte.parte'])
                ->whereHas('expediente.audiencia.audienciaParte.parte', function($q) use($genero_id, $edad_inicial, $edad_final){

                    if($genero_id) {
                        $q->where('genero_id', $genero_id);
                    }
                    if($edad_inicial) {
                        $q->whereRaw('edad::integer >= ?', $edad_inicial);
                    }
                    if($edad_final) {
                        $q->whereRaw('edad::integer <= ?', $edad_final);
                    }
                    //Solo se toman en cuenta los solicitantes
                    $q->where('tipo_parte_id', self::SOLICITANTE_ID);
                });
        }

        return $q;
    }

    private function filtroPorCaracteristicasSolicitanteAudienciaParte($request, $q)
    {
        if($request->get('genero_id') || $request->get('edad_inicial') || $request->get('edad_final') ){
            $genero_id = $request->get('genero_id');
            $edad_inicial = $request->get('edad_inicial');
            $edad_final = $request->get('edad_final');

            if($genero_id) {
                $q->where('partes.genero_id', $genero_id);
            }
            if($edad_inicial) {
                $q->whereRaw('partes.edad::integer >= ?', $edad_inicial);
            }
            if($edad_final) {
                $q->whereRaw('partes.edad::integer <= ?', $edad_final);
            }
            //Solo se toman en cuenta los solicitantes
            $q->where('partes.tipo_parte_id', self::SOLICITANTE_ID);
        }

        return $q;
    }

    private function filtroPorCaracteristicasPartes($request, $q)
    {
        $fecha_inicial = $request->get('fecha_inicial');
        $fecha_final = $request->get('fecha_final');

        $q->with(['expediente','expediente.audiencia','expediente.audiencia.audienciaParte','expediente.audiencia.audienciaParte.parte'])
            ->whereHas('expediente.audiencia.audienciaParte', function($q) use($fecha_inicial, $fecha_final){

                if($fecha_inicial) {
                    $q->whereRaw('created_at::date <= ?', $fecha_inicial);
                }
                if($fecha_final) {
                    $q->whereRaw('created_at::date >= ?', $fecha_final);
                }
            });

        return $q;
    }
}

