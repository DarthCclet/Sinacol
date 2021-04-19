<?php


namespace App\Services;


use App\Audiencia;
use App\AudienciaParte;
use App\Expediente;
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
    const TERMINACION_INCOMPETENCIA_EN_AUDIENCIA = 6;
    const INCOMPETENCIA_EN_AUDIENCIA = 13;

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

        # Las solicitudes presentadas se evaluan por fecha de recepcion
        if($request->get('fecha_inicial')){
            $q->whereRaw('fecha_recepcion::date >= ?', $request->get('fecha_inicial'));
        }
        if($request->get('fecha_final')){
            $q->whereRaw('fecha_recepcion::date <= ?', $request->get('fecha_final'));
        }

        # Hacemos el join con centros para reprotar agrupado por centro
        $q->join('centros','solicitudes.centro_id','=','centros.id');

        # Si viene solicitud de desagregación mandamos todos los registros
        # de lo contrario mandamos registros agrupados por centro
        if ($request->get('tipo_reporte') == 'agregado') {
            # Seleccionamos la abreviatura del nombre y su cuenta
            $q->select('centros.abreviatura', DB::raw('count(*)'))->groupBy('centros.abreviatura');
        }else{
            $q->with(['objeto_solicitudes']);
            $q->select('solicitudes.id as sid','solicitudes.*','centros.abreviatura');
            # Ordenamos por el centro y por la fecha de recepción para mostrar en el listado desagregado
            $q->orderBy('centros.abreviatura')->orderBy('solicitudes.fecha_recepcion');
        }

        # Sólo las de trabajador y patron individual por default.
        # Si viene en el filtro alguno en específico se filtra por ese
        if (!$request->get('tipo_solicitud_id')) {
            $q->whereIn('tipo_solicitud_id', [self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);
        } else {
            $q->where('tipo_solicitud_id', $request->get('tipo_solicitud_id'));
        }

        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        //Dejamos fuera los no consultables
        $this->noReportables($q);

        return $q->get();
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
        $q->whereNotNull('fecha_ratificacion');

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('centros','solicitudes.centro_id','=','centros.id');

        # Si viene solicitud de desagregación mandamos todos los registros
        # de lo contrario mandamos registros agrupados por centro
        if($request->get('tipo_reporte')=='agregado') {
            # Seleccionamos la abreviatura del nombre y su cuenta
            $q->select('centros.abreviatura', 'inmediata');
        }else{
            $q->with(['objeto_solicitudes']);
            $q->select('solicitudes.id as sid','solicitudes.*','centros.abreviatura');
            # Ordenamos por el centro y por la fecha de recepción para mostrar en el listado desagregado
            $q->orderBy('centros.abreviatura')->orderBy('solicitudes.fecha_recepcion');
        }

        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        //Se filtran las no reportables
        $this->noReportables($q);

        # Sólo las de trabajador y patron individual por default.
        # Si viene en el filtro alguno en específico se filtra por ese
        if (!$request->get('tipo_solicitud_id')) {
            $q->whereIn('tipo_solicitud_id', [self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);
        } else {
            $q->where('tipo_solicitud_id', $request->get('tipo_solicitud_id'));
        }

        if ($request->get('tipo_reporte') == 'agregado') {
            $res = $q->get()->sortBy('abreviatura');
            $inmediata = $res->where('inmediata', true)->groupBy('abreviatura');
            $normal = $res->where('inmediata', false)->groupBy('abreviatura');
            return [$inmediata, $normal];
        } else {
            return $res = $q->get()->sortBy('abreviatura');
        }
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
        $q->select('centros.abreviatura', 'tipo_notificacion_id', 'audiencias.numero_audiencia',
                   'audiencias.id as audiencia_id', 'audiencias.folio', 'audiencias.anio','audiencias.expediente_id',
                   'expedientes.folio as expediente_folio','expedientes.anio as expediente_anio','solicitudes.id as solicitud_id', 'parte_id' );
        $q->selectRaw('audiencias_partes.created_at::date as fecha_citatorio');

        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteAudienciaParte($request, $q);

        //Dejamos fuera los no consultables
        $this->noReportables($q);

        //Sólo las de trabajador y patron individual
        if (!$request->get('tipo_solicitud_id')) {
            $q->whereIn('solicitudes.tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);
        } else {
            $q->where('solicitudes.tipo_solicitud_id', $request->get('tipo_solicitud_id'));
        }

        $q->whereNull('audiencias.deleted_at');
        $q->whereNull('expedientes.deleted_at');
        $q->whereNull('solicitudes.deleted_at');
        $q->whereNotNull('audiencias_partes.tipo_notificacion_id');

        # Dado que para las solicitudes inmediatas no hay citatorio...
        $q->where('solicitudes.inmediata', false);

        # Dado que los citatorios sólo se otorgan para citados...
        $q->where('partes.tipo_parte_id', self::CITADO);

        # Regla que programó Diana.
        $q->whereRaw('(audiencias_partes.created_at::date > solicitudes.fecha_ratificacion::date and audiencias_partes.tipo_notificacion_id = 1) = false');

        if ($request->get('tipo_reporte') == 'agregado') {

            $res = $q->get()->sortBy('abreviatura');

            //Entrega Solicitante
            $entrega_solicitante = $res->where('tipo_notificacion_id', self::CITATORIO_POR_SOLICITANTE)
                ->groupBy('abreviatura')
                ->map(
                    function ($en_centro, $centro) {
                        return count($en_centro);
                    }
                )->toArray();

            $entrega_notificador = $res->where('tipo_notificacion_id', self::CITATORIO_POR_NOTIFICADOR)
                ->groupBy('abreviatura')
                ->map(
                    function ($en_centro, $centro) {
                        return count($en_centro);
                    }
                )->toArray();

            $entrega_notificador_cita = $res->where('tipo_notificacion_id', self::CITATORIO_POR_NOTIFICADOR_ACOMPANIADO)
                ->groupBy('abreviatura')
                ->map(
                    function ($en_centro, $centro) {
                        return count($en_centro);
                    }
                )->toArray();

            //En primera audiencia

            $citatorio_en_primera_audiencia = $res->where('numero_audiencia', 1)->unique('audiencia_id')
                ->groupBy('abreviatura')
                ->map(
                    function ($en_centro, $centro) {
                        return count($en_centro);
                    }
                )->toArray();
            //En segunda audiencia
            $citatorio_en_segunda_audiencia = $res->where('numero_audiencia', 2)->unique('audiencia_id')
                ->groupBy('abreviatura')
                ->map(
                    function ($en_centro, $centro) {
                        return count($en_centro);
                    }
                )->toArray();

            //En tercera audiencia
            $citatorio_en_tercera_audiencia = $res->where('numero_audiencia', 3)->unique('audiencia_id')
                ->groupBy('abreviatura')
                ->map(
                    function ($en_centro, $centro) {
                        return count($en_centro);
                    }
                )->toArray();

            $res =  compact(
                'entrega_solicitante',
                'entrega_notificador',
                'entrega_notificador_cita',
                'citatorio_en_primera_audiencia',
                'citatorio_en_segunda_audiencia',
                'citatorio_en_tercera_audiencia'
            );

            return $res;
        }

        $q->orderBy('centros.abreviatura')->orderBy('audiencias_partes.created_at');
        return $q->get();
    }


    public function incompetencias($request)
    {
        if ($request->get('tipo_reporte') == 'agregado') {
            $en_ratificacion = $this->incompetenciasEnEtapa($request, 'ratificacion');
            $en_audiencia = $this->incompetenciasEnEtapa($request, 'audiencia');
        }
        return compact('en_ratificacion','en_audiencia');
    }

    /**
     * Con respecto a las incompetencias
     * @param $request
     * @param string $etapa
     * @return array
     */
    public function incompetenciasEnEtapa($request, $etapa = 'ratificacion')
    {
        $q = (new SolicitudFilter(Solicitud::query(), $request))
            ->searchWith(Solicitud::class)
            ->filter(false);

        //Las solicitudes confirmadas se evaluan por fecha de ratificacion
        if ($request->get('fecha_inicial')) {
            $q->whereRaw('fecha_recepcion::date >= ?', $request->get('fecha_inicial'));
        }
        if ($request->get('fecha_final')) {
            $q->whereRaw('fecha_recepcion::date <= ?', $request->get('fecha_final'));
        }

        //Seleccionamos la abreviatura del nombre y su cuenta
        $q->select('centros.abreviatura', DB::raw('count(*)'))->groupBy('centros.abreviatura');

        //Hacemos el join con centros para reprotar agrupado por centro
        $q->join('centros', 'solicitudes.centro_id', '=', 'centros.id');
        $q->leftJoin('expedientes', 'solicitudes.id', '=', 'expedientes.solicitud_id');
        $q->leftJoin('audiencias', 'expedientes.id', '=', 'audiencias.expediente_id');


        //Se aplican filtros por características del solicitante
        $this->filtroPorCaracteristicasSolicitanteSolicitud($request, $q);

        $q->whereNull('audiencias.deleted_at');
        $q->whereNull('expedientes.deleted_at');
        $q->whereNull('solicitudes.deleted_at');

        if ($etapa == 'ratificacion') {
            $q->whereNull('fecha_ratificacion');
            $q->has('documentosComentadosComoIncompetencia');
            $q->where('tipo_incidencia_solicitud_id', self::INCOMPETENCIA_EN_RATIFICACION);
        }
        else{
            $q->whereNotNull('fecha_ratificacion');
            $q->whereHas('expediente.audiencia.documentos', function ($qq){
               return $qq->where('clasificacion_archivo_id', self::INCOMPETENCIA_EN_AUDIENCIA);
            });
        }

        //Se filtran las no reportables
        $this->noReportables($q);

        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);
//dump($q->toSql());
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
        $q->whereNull('expedientes.deleted_at');

        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('count','abreviatura');

        return $res;
    }

    /**
     * Acerca de los convenios de conciliacion
     * @param $request
     * @return void
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
        $q->whereNull('expedientes.deleted_at');
        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('sum','abreviatura');

        return $res;
    }

    /**
     * Convenios con ratificación
     * @param $request
     * @return mixed
     */
    public function conveniosRatificacion($request)
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
        $q->whereNull('expedientes.deleted_at');
        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('sum','abreviatura');

        return $res;
    }

    /**
     * No conciliación
     * @param $request
     * @return mixed
     */
    public function noConciliacion($request)
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
        $q->whereNull('expedientes.deleted_at');
        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('sum','abreviatura');

        return $res;
    }

    /**
     * Audiencias
     * @param $request
     * @return mixed
     */
    public function audiencias($request)
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
        $q->whereNull('expedientes.deleted_at');
        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('sum','abreviatura');

        return $res;
    }

    /**
     * Pagos diferidos
     * @param $request
     * @return mixed
     */
    public function pagosDiferidos($request)
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
        $q->whereNull('expedientes.deleted_at');
        //Sólo las de trabajador y patron individual
        $q->whereIn('tipo_solicitud_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL]);

        $res = $q->get()->sortBy('abreviatura')->pluck('sum','abreviatura');

        return $res;
    }


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
        if($request->get('genero_id') || (is_array($request->get('grupo_id')) && count($request->get('grupo_id'))) || $request->get('tipo_persona_id')){
            $genero_id = $request->get('genero_id');
            $grupo_id = $request->get('grupo_id');
            $tipo_persona_id = $request->get('tipo_persona_id');
            $q->with(['expediente','expediente.audiencia','expediente.audiencia.audienciaParte.parte'])
                ->whereHas('expediente.audiencia.audienciaParte.parte', function($q) use($genero_id, $grupo_id, $tipo_persona_id){

                    # Por el género
                    if($genero_id) {
                        $q->where('genero_id', $genero_id);
                    }

                    # Por el  o los grupos etarios
                    if(is_array($grupo_id) && count($grupo_id)) {

                        $q->where(function($qq) use ($grupo_id) {
                            foreach ($grupo_id as $idx => $grupo) {
                                list($ini,$fin) = explode('-', $grupo);
                                if($idx == 0) {
                                    $qq->whereRaw('edad::integer BETWEEN ? AND ?', [$ini, $fin]);
                                }else{
                                    $qq->orWhereRaw('edad::integer BETWEEN ? AND ?', [$ini, $fin]);
                                }
                            }
                        });

                    }
                    # Por el tipo de persona
                    if($tipo_persona_id) {
                        $q->where('tipo_persona_id', $tipo_persona_id);
                    }

                    # Sólo se toman en cuenta los solicitantes
                    $q->where('tipo_parte_id', self::SOLICITANTE_ID);
                });
        }

        return $q;
    }

    /**
     * Filtra características del solicitante para una audiencia
     * @param $request
     * @param $q
     * @return mixed
     */
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

    /**
     * Marca borrado lógico de expedientes duplicados por bug.
     * Se toma como bueno el primer registro (id más bajo en expedientes)
     * @param bool $dry_run Si se pasa true no se ejecuta el borrado sólo se regresan los borrables.
     * @return
     */
    public function seteaDeletedAtDeExpedientesDuplicadosConMismaSolicitudId($dry_run = false){

        $dbh = Expediente::withTrashed()->select(DB::raw('count(*), solicitud_id'))
            ->groupBy('solicitud_id')
            ->havingRaw('count(*) > 1');
        $duplicados =  $dbh->get();
        $ids_duplicados = $duplicados->pluck('solicitud_id')->toArray();

        $borrados = Expediente::onlyTrashed()->whereIn('solicitud_id', $ids_duplicados)->get();
        $idsborrados = $borrados->unique('solicitud_id')->pluck('solicitud_id')->toArray();

        $no_borrados = $duplicados->reject(function ($val, $key) use ($idsborrados){
            return in_array($val->solicitud_id, $idsborrados);
        });
        $ids_no_borrados = $no_borrados->unique('solicitud_id')->pluck('solicitud_id')->toArray();

        $no_borrables = Expediente::whereIn('solicitud_id', $ids_no_borrados)->orderBy('solicitud_id')->orderBy('id')->get();

        $no_borrables_ids = $no_borrables->unique('solicitud_id')->pluck('solicitud_id','id')->toArray();

        $borrables = Expediente::whereIn('solicitud_id', $ids_duplicados)->whereNull('deleted_at')->get()->reject(function ($val, $key) use ($no_borrables_ids){
            return isset($no_borrables_ids[$val->id]);
        });

        $borrables->each(function ($item, $key) use ($dry_run) {
            if(!$dry_run) $item->delete();
        });

        return $borrables;
    }
}

