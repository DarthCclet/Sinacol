<?php


namespace App\Services;


use App\Traits\EstilosSpreadsheets;
use Illuminate\Support\Facades\Log;
use Lavary\Menu\ServiceProvider;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReporteOperativoService
{
    use EstilosSpreadsheets;

    /**
     * @var ReporteOperativoService
     */
    protected $service;

    /**
     * Días para el archivado de no ratificadas
     */
    const DIAS_PARA_ARCHIVAR = 20;

    /**
     * ExcelReporteOperativoService constructor.
     * @param ReporteOperativoService $service
     */
    public function __construct(ReporteOperativoService $service)
    {
        $this->service = $service;
    }

    /**
     * Genera el reporte operativo en excel
     * @param $sheet
     * @param $request
     */
    public function reporte($sheet, $request)
    {
        # Solicitudes confirmadas
        $qSolicitudesRatificadas = $this->service->solicitudes($request);
        $qSolicitudesArchivadasNoConfirmacion = $this->service->solicitudes($request);
        $qSolicitudesPresentadas = $this->service->solicitudes($request);
        $sheet->setCellValue('B2', $qSolicitudesRatificadas->where('ratificada', true)->count());

        # Archivadas por no confirmación
        // regla de negocio: No ratificados por más de 7 días desde su creación.
        $sheet->setCellValue('B3', $qSolicitudesArchivadasNoConfirmacion->where('ratificada', false)
            ->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        # Solicitudes presentadas
        $sheet->setCellValue('B5', $qSolicitudesPresentadas->count());

        # Solicitudes por concepto: total, confirmadas, archivadas por no confirmar, por confirmar
        # tipo de solicitud trabajador o patron

        $pptrab = ($this->service->solicitudes($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_INDIVIDUAL);
        $sheet->setCellValue('B8', $pptrab->count());
        $sheet->setCellValue('C8', $pptrab->where('ratificada', true)->count());
        $sheet->setCellValue('D8', ($this->service->solicitudes($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_INDIVIDUAL)->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E8', ($this->service->solicitudes($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_INDIVIDUAL)->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());

        $pppatr = ($this->service->solicitudesRatificacion($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL);
        $sheet->setCellValue('B9', $pppatr->count());
        $sheet->setCellValue('C9', $pppatr->where('ratificada',true)->count());
        $sheet->setCellValue('D9', ($this->service->solicitudes($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL)->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E9', ($this->service->solicitudes($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL)->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());

        # con asistencia de personal o por los usuarios
        $caspc = ($this->service->solicitudes($request))->whereNotNull('user_captura_id');
        $sheet->setCellValue('B12', $caspc->count());
        $sheet->setCellValue('C12', $caspc->where('ratificada', true)->count());
        $sheet->setCellValue('D12', ($this->service->solicitudes($request))->whereNotNull('user_captura_id')->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E12', ($this->service->solicitudes($request))->whereNotNull('user_captura_id')->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());

        $sol = ($this->service->solicitudes($request))->whereNull('user_captura_id');
        $sheet->setCellValue('B13', $sol->count());
        $sheet->setCellValue('C13', $sol->where('ratificada', true)->count());
        $sheet->setCellValue('D13', ($this->service->solicitudes($request))->whereNull('user_captura_id')->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E13', ($this->service->solicitudes($request))->whereNull('user_captura_id')->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());

        # por género
        //Hombres
        $solgenh = ReportesService::caracteristicasSolicitante(($this->service->solicitudes($request)), 'partes', ReportesService::GENERO_MASCULINO_ID,null,null);
        $solgenhpr = ReportesService::caracteristicasSolicitante(($this->service->solicitudes($request)), 'partes', ReportesService::GENERO_MASCULINO_ID,null,null);
        $solgenhar = ReportesService::caracteristicasSolicitante(($this->service->solicitudes($request)), 'partes', ReportesService::GENERO_MASCULINO_ID,null,null);
        $sheet->setCellValue('B16', $solgenh->count());
        $sheet->setCellValue('C16', $solgenh->where('ratificada', true)->count());
        $sheet->setCellValue('D16', $solgenhar->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E16', $solgenhpr->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());
        //Mujeres
        $solgenm = ReportesService::caracteristicasSolicitante(($this->service->solicitudes($request)), 'partes', ReportesService::GENERO_FEMENINO_ID,null,null);
        $solgenmpr = ReportesService::caracteristicasSolicitante(($this->service->solicitudes($request)), 'partes', ReportesService::GENERO_FEMENINO_ID,null,null);
        $solgenmar = ReportesService::caracteristicasSolicitante(($this->service->solicitudes($request)), 'partes', ReportesService::GENERO_FEMENINO_ID,null,null);
        $sheet->setCellValue('B17', $solgenm->count());
        $sheet->setCellValue('C17', $solgenm->where('ratificada', true)->count());
        $sheet->setCellValue('D17', $solgenmar->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E17', $solgenmpr->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());

        # Por grupo etáreo
        /** @var Array[Builder] $solget Arreglo que contiene objetos Builder para las solicitudes totalizadas de grupo etario  */
        $solget=[];
        /** @var Array[Builder] $solget Arreglo que contiene objetos Builder para las solicitudes por ratificar  */
        $solgetpr=[];
        /** @var Array[Builder] $solgetpr Arreglo que contiene objetos Builder para las solicitudes archivadas  */
        $solgetar=[];
        /** @var integer $rowget Fila de grupo etario */
        $rowget = 21;
        foreach(ReportesService::gruposEtarios() as $grupo => $descripcion){
            $solget[$grupo] =  (ReportesService::caracteristicasSolicitante(clone $this->service->solicitudes($request), 'partes', null, $grupo,null));
            $solgetpr[$grupo] =  (ReportesService::caracteristicasSolicitante(clone $this->service->solicitudes($request), 'partes', null, $grupo,null));
            $solgetar[$grupo] =  (ReportesService::caracteristicasSolicitante(clone $this->service->solicitudes($request), 'partes', null, $grupo,null));
            $sheet->setCellValue('B'.$rowget, $solget[$grupo]->count());
            $sheet->setCellValue('C'.$rowget, $solget[$grupo]->where('ratificada', true)->count());
            $sheet->setCellValue('D'.$rowget, $solgetar[$grupo]->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
            $sheet->setCellValue('E'.$rowget, $solgetpr[$grupo]->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());
            $rowget++;
        }

        # Por "tipo de conflicto" esto es objeto de la conciliacón

        // Hay una particularidad en esta tabla, sólo dos conceptos comparten nombre para patrones y trabajadores las demás columnas aparecerán en cero

        /** @var int $rowobj row donde empieza la tabla de objetos */
        $rowobj = $rowget + 1;
        $sheet->setCellValue('A'.$rowobj, "Solicitudes por concepto (total, confirmadas, no confirmadas y por confirmar) Tipo de Conflicto");
        $sheet->setCellValue('B'.$rowobj, "TOTAL PATRÓN");
        $sheet->setCellValue('C'.$rowobj, "TOTAL TRABAJADOR");
        $sheet->setCellValue('D'.$rowobj, "TOTAL");

        /** @var int $rowinidat row donde empiezam los datos de la tabla, esta se utiliza para indicar desde qué punto empieza la copia de estilo */
        $rowinidat = $rowobj +1;

        // Copiamos el estilo del último encabezado fijo a los generados dinámicamente
        $sheet->duplicateStyle($sheet->getStyle('A20'),'A'.$rowobj.':D'.$rowobj);

        $rowobj++;

        /** @var array indica si se ha visto o no el nombre del objeto */
        $visto = [];

        foreach(ReportesService::getObjetosFiltrables(true) as $id => $objeto){

            $cant = $this->service->solicitudes($request)->whereHas('objeto_solicitudes', function ($q) use ($objeto) {
                $q->where('objeto_solicitud_id', $objeto->id);
            })->count();

            if(!isset($visto[$objeto->nombre])) {
                $visto[$objeto->nombre] = true;
                $sheet->setCellValue('A' . $rowobj, $objeto->nombre);
                $sheet->setCellValue('B' . $rowobj, 0);
                $sheet->setCellValue('C' . $rowobj, 0);
                $sheet->setCellValue('D' . $rowobj, "=SUM(B$rowobj:C$rowobj)");
                if ($objeto->tipo_objeto_solicitudes_id == ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL) {
                    $sheet->setCellValue('B' . $rowobj, $cant);
                } else {
                    $sheet->setCellValue('C' . $rowobj, $cant);
                }
                $rowobj++;
                continue;
            }

            if ($objeto->tipo_objeto_solicitudes_id == ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL) {
                $sheet->setCellValue('B' . ($rowobj -1), $cant);
            } else {
                $sheet->setCellValue('C' . ($rowobj -1), $cant);
            }

            Log::info($rowobj." Objeto: ".$objeto->tipo_objeto_solicitudes_id." ..=> ".$objeto->id." ". $objeto->nombre);
        }

        // Copiamos el estilo de la última celda de datos de la tabla fija a la tabla generada dinámicamente
        $sheet->duplicateStyle($sheet->getStyle('A21'),'A'.$rowinidat.':A'.($rowobj-1));
        $sheet->duplicateStyle($sheet->getStyle('B21'),'B'.$rowinidat.':D'.($rowobj-1));

        # Por "concepto" esto es por industria según catalogo del poder judicial

        //Solicitudes por Rama Industrila

        /** @var int $rowind row donde empieza la tabla de objetos */
        $rowind = $rowobj + 1;
        $sheet->setCellValue('A'.$rowind, "Solicitudes por concepto (total, confirmadas, no confirmadas y por confirmar) Rama Industrial");
        $sheet->setCellValue('B'.$rowind, "TOTAL");
        $sheet->setCellValue('C'.$rowind, "CONFIRMADAS");
        $sheet->setCellValue('D'.$rowind, "ARCHIVADAS POR NO CONFIRMACIÓN");
        $sheet->setCellValue('E'.$rowind, "POR CONFIRMAR");

        $rowinidat = $rowind;
        // Copiamos el estilo del último encabezado fijo a los generados dinámicamente
        $sheet->duplicateStyle($sheet->getStyle('A20'),'A'.$rowind.':E'.$rowind);

        $rowind++;

        $sheet->setCellValue('A'.$rowind, 0);
        $sheet->setCellValue('B'.$rowind, 0);
        $sheet->setCellValue('C'.$rowind, 0);
        $sheet->setCellValue('D'.$rowind, 0);
        $sheet->setCellValue('E'.$rowind, 0);

        foreach(ReportesService::getIndustria() as $industria_id => $industria){
            //dump($industria_id ." => ".$industria);
            $total = $this->service->solicitudes($request)
                ->whereHas('giroComercial', function ($q) use ($industria_id) {
                    $q->where('industria_id', $industria_id);
            })->count();

            $confirmadas = $this->service->solicitudes($request)->where('ratificada', true)
                ->whereHas('giroComercial', function ($q) use ($industria_id) {
                    $q->where('industria_id', $industria_id);
            })->count();

            $archivadas = (clone $this->service->solicitudes($request))->where('ratificada', false)
                ->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )
                ->whereHas('giroComercial', function ($q) use ($industria_id) {
                    $q->where('industria_id', $industria_id);
            })->count();

            $porconfirmar = (clone $this->service->solicitudes($request))->where('ratificada', false)
                ->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )
                ->whereHas('giroComercial', function ($q) use ($industria_id) {
                    $q->where('industria_id', $industria_id);
            })->count();

            $sheet->setCellValue('A'.$rowind, $industria);
            $sheet->setCellValue('B'.$rowind, $total);
            $sheet->setCellValue('C'.$rowind, $confirmadas);
            $sheet->setCellValue('D'.$rowind, $archivadas);
            $sheet->setCellValue('E'.$rowind, $porconfirmar);

            $rowind++;
        }
        $sheet->setCellValue('A'.$rowind, "TOTAL");
        $sheet->setCellValue('B'.$rowind, "=SUM(B".($rowobj + 2).":B$rowind)");
        $sheet->setCellValue('C'.$rowind, "=SUM(C".($rowobj + 2).":C$rowind)");
        $sheet->setCellValue('D'.$rowind, "=SUM(D".($rowobj + 2).":D$rowind)");
        $sheet->setCellValue('E'.$rowind, "=SUM(E".($rowobj + 2).":E$rowind)");

        // Copiamos el estilo de la última celda de datos de la tabla fija a la tabla generada dinámicamente
        $sheet->duplicateStyle($sheet->getStyle('A21'),'A'.($rowinidat+1).':A'.($rowind));
        $sheet->duplicateStyle($sheet->getStyle('B21'),'B'.($rowinidat+1).':E'.($rowind));

        //////////////////////////////////////////////////////////////////////////////////////////////////////////
        # Sobre las solicitudes inmediatas (le llaman convenio con ratificación en el reporte)
        //////////////////////////////////////////////////////////////////////////////////////////////////////////

        # F2 Incompetencias
        $incompetencias = (clone $this->service->solicitudes($request))->has('documentosComentadosComoIncompetencia')
            ->where('tipo_incidencia_solicitud_id', ReportesService::INCOMPETENCIA_EN_RATIFICACION);
        $sheet->setCellValue('G2', $incompetencias->count());

        # F3 Incompetencia detectada en audiencia
        $incompetenciasEnAudiencia = (clone $this->service->solicitudes($request))->whereHas('expediente.audiencia.documentos', function ($qq){
            return $qq->where('clasificacion_archivo_id', ReportesService::INCOMPETENCIA_EN_AUDIENCIA);
        });
        $sheet->setCellValue('G3', $incompetenciasEnAudiencia->count());

        # F4 Competencias
        // Para saber las competentes entonces seleccionamos todas las solicitudes y eliminamos los ids de incompetencias detectadas en ratificación y en audiencia
        $solicitudesIncompetenciasIds = $incompetencias->get()->merge($incompetenciasEnAudiencia->get())->pluck('id')->toArray();
        $competencias = (clone $this->service->solicitudes($request))->whereNotIn('id', $solicitudesIncompetenciasIds)->count();
        $sheet->setCellValue('G4', $competencias);

        # F7 Número de solicitudes inmediatas (ratificaciones le llaman en el reporte)
        $inmediatas = (clone $this->service->solicitudes($request))->where('inmediata', true)->count();
        $sheet->setCellValue('G7', $inmediatas);

        # G8 Monto de convenio con ratificaciones
        $monto_hubo_convenio = (clone $this->service->convenios($request))->where('inmediata', true)
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)->get()
            ->sum('monto');
        $sheet->setCellValue('G8', $monto_hubo_convenio);

        # G9 número de beneficios o prestaciones no económicas derivadas de ratificación
        $convenio_no_economico = (clone $this->service->convenios($request))->where('inmediata', true)
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)
            ->whereIn('concepto_pago_resoluciones_id',[
                                                        ReportesService::CONCEPTO_PAGO_GRATIFICACION_EN_ESPECIE,
                                                        ReportesService::CONCEPTO_PAGO_RECONOCIMIENTO_DERECHOS,
                                                        ReportesService::CONCEPTO_PAGO_OTRO])
            ->where(function ($query) {
                $query->where('monto', 0)
                    ->orWhereNull('monto');
            })
            ->get()
            ->count();
        $sheet->setCellValue('G9', $convenio_no_economico);

        //////////////////////////////////////////////////////////////////
        # 3 Sobre los Citatorios
        ///////////////////////////////////////////////////////////

        # I2 Primeras audiencias para las que se emitió citatorio
        $primeras_aud_citatorio = (clone $this->service->audiencias($request))->whereHas('audienciaParte', function ($q){
            $q->whereIn('tipo_notificacion_id', [
                ReportesService::CITATORIO_POR_SOLICITANTE,
                ReportesService::CITATORIO_POR_NOTIFICADOR,
                ReportesService::CITATORIO_POR_NOTIFICADOR_ACOMPANIADO]);
        })->count();
        $sheet->setCellValue('J2', $primeras_aud_citatorio);

        # I3 Número de citatorios emitidos para primera audiencia
        $citatorios_para_prim_aud = (clone $this->service->citatorios($request))
            ->where('audiencias.numero_audiencia', 1)->whereIn('tipo_notificacion_id', [
                ReportesService::CITATORIO_POR_SOLICITANTE,
                ReportesService::CITATORIO_POR_NOTIFICADOR,
                ReportesService::CITATORIO_POR_NOTIFICADOR_ACOMPANIADO])->count();
        $sheet->setCellValue('J3', $citatorios_para_prim_aud);

        # I6 Del total de citatorios emitidos
        $total_citatorios_emitidos = (clone $this->service->citatorios($request))
            ->whereIn('tipo_notificacion_id', [
                ReportesService::CITATORIO_POR_SOLICITANTE,
                ReportesService::CITATORIO_POR_NOTIFICADOR,
                ReportesService::CITATORIO_POR_NOTIFICADOR_ACOMPANIADO])->count();
        $sheet->setCellValue('J6', $total_citatorios_emitidos);

        # I7 Número de citatorios notificados por el solicitante
        $citatorios_x_solicitante = (clone $this->service->citatorios($request))
            ->whereIn('tipo_notificacion_id', [ReportesService::CITATORIO_POR_SOLICITANTE])->count();
        $sheet->setCellValue('J7', $citatorios_x_solicitante);

        # I8 Número de citatorios notificados por personal del CFCRL
        $citatorios_x_notificador = (clone $this->service->citatorios($request))
            ->whereIn('tipo_notificacion_id', [ReportesService::CITATORIO_POR_NOTIFICADOR])->count();
        $sheet->setCellValue('J8', $citatorios_x_notificador);

        # I9 Número de citatorios notificados por personal del CFCRL en compañía del solicitante
        $citatorios_x_notificador_acompaniado = (clone $this->service->citatorios($request))
            ->whereIn('tipo_notificacion_id', [ReportesService::CITATORIO_POR_NOTIFICADOR_ACOMPANIADO])->count();
        $sheet->setCellValue('J9', $citatorios_x_notificador_acompaniado);

        //////////////////////////////////////////////////////////////////
        # 3 Sobre las audiencias
        ///////////////////////////////////////////////////////////

        # K2 Total de Audiencias de Conciliación
        $total_audiencias = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)->count();
        $sheet->setCellValue('L2', $total_audiencias);

        # K3 Conciliacion en 1a audiencia
        $concilia_en_1a = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)
            ->where('numero_audiencia', 1)
            ->count();
        $sheet->setCellValue('L3', $concilia_en_1a);

        # K4 Conciliacion en 2a audiencia
        $concilia_en_2a = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)
            ->where('numero_audiencia', 2)
            ->count();
        $sheet->setCellValue('L4', $concilia_en_2a);

        # K5 Conciliacion en 3a audiencia
        $concilia_en_3a = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)
            ->where('numero_audiencia', 3)
            ->count();
        $sheet->setCellValue('L4', $concilia_en_3a);

        # K5 Conciliacion en 4a audiencia o mas
        $concilia_en_4a = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)
            ->where('numero_audiencia', '>=', 4)
            ->count();
        $sheet->setCellValue('L5', $concilia_en_4a);

        # M3 Archivo por falta de interés
        $archivo_falta_interes = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_ARCHIVADO)
            ->count();
        $sheet->setCellValue('N3', $archivo_falta_interes);

        # M4 solicita nueva audiencia
        $sol_nueva_fecha = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_NO_CONVENIO_DESEA_AUDIENCIA)
            ->count();
        $sheet->setCellValue('N4', $sol_nueva_fecha);

        # M5 No conciliacion
        $no_conciliaciones = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_NO_HUBO_CONVENIO)
            ->count();
        $sheet->setCellValue('N5', $no_conciliaciones);

        # M6 Convenio
        $hubo_convenios = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)
            ->count();
        $sheet->setCellValue('N6', $hubo_convenios);

        //////////////////////////////////////////////////////////////////
        # 4 Sobre las audiencias Ver con Diana si los querys son correctos, o de donde se saca el dato de que no se presentó el citado o el solicitante
        ///////////////////////////////////////////////////////////

        # O3 Total de archivos por falta de interés

        $archivo_falta_interes = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_ARCHIVADO)
            ->whereIn('solicitudes.tipo_solicitud_id',[
                ReportesService::SOLICITUD_INDIVIDUAL, ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL])
            ->count();
        $sheet->setCellValue('P2', $archivo_falta_interes);

        # O4 En cuántos no se presentó el solicitante trabajador

        $archivo_falta_interes_solicitante = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_ARCHIVADO)
            ->whereIn('solicitudes.tipo_solicitud_id',[ReportesService::SOLICITUD_INDIVIDUAL])
            ->count();
        $sheet->setCellValue('P3', $archivo_falta_interes_solicitante);

        # O5 En cuántos no se presentó el solicitante patrón

        $archivo_falta_interes_patron = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_ARCHIVADO)
            ->whereIn('solicitudes.tipo_solicitud_id',[ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL])
            ->count();
        $sheet->setCellValue('P4', $archivo_falta_interes_patron);

        //////////////////////////////////////////////////////////////////
        # 5 Conclusión  Ver con Diana, si es la forma correcta de saber si no se presentó el citado o el solicitante
        ///////////////////////////////////////////////////////////

        # Q2 Total  de Constancias de No Conciliaciones
        $total_constancias_no_conciliacion = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_NO_HUBO_CONVENIO)
            ->count();
        $sheet->setCellValue('R2', $total_constancias_no_conciliacion);

        # Q3 Número de contancias de no conciliación por incomparecencia del citado
        $no_conciliacion_nocomparecencia = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_NO_HUBO_CONVENIO)
            ->where('tipo_terminacion_audiencia_id', ReportesService::TERMINACION_AUDIENCIA_NO_COMPARECENCIA_CITADO)
            ->count();
        $sheet->setCellValue('R3', $no_conciliacion_nocomparecencia);

        # Q4 Número de Constancias de No Conciliación por no acuerdo
        $sheet->setCellValue('R4', ($total_constancias_no_conciliacion - $no_conciliacion_nocomparecencia));

        /////////

        # S2 Total de convenios
        $total_convenios = (clone $this->service->audiencias($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)
            ->count();
        $sheet->setCellValue('T2', $no_conciliacion_nocomparecencia);

        # S3 Monto desglosado de los convenios
        $monto_convenios = (clone $this->service->convenios($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)->get()
            ->sum('monto');
        $sheet->setCellValue('T3', $monto_convenios);

        # S4 Beneficios o prestaciones no económicas
        $beneficios = (clone $this->service->convenios($request))
            ->where('resolucion_id', ReportesService::RESOLUCIONES_HUBO_CONVENIO)
            ->whereIn('concepto_pago_resoluciones_id',[
                ReportesService::CONCEPTO_PAGO_GRATIFICACION_EN_ESPECIE,
                ReportesService::CONCEPTO_PAGO_RECONOCIMIENTO_DERECHOS,
                ReportesService::CONCEPTO_PAGO_OTRO])
            ->where(function ($query) {
                $query->where('monto', 0)
                    ->orWhereNull('monto');
            })
            ->get()
            ->count();
        $sheet->setCellValue('T4', $beneficios);


        $num_pagos_dif = (clone $this->service->audiencias($request))
            ->has('pagosDiferidos', 1)->with('pagosDiferidos')
            ->get()->count();
        $sheet->setCellValue('T7', $num_pagos_dif);

        //ToDo: Qué onda con la columna U ?

        $monto_pagos_dif = (clone $this->service->pagosDiferidos($request))
            ->has('pagosDiferidos', '=', 1)
            ->get()
            ->map(function ($k, $v){
                return $k->pagosDiferidos->sum('monto');
            });
        $sheet->setCellValue('V7', $monto_pagos_dif->sum());

        $num_pagos_parciales = (clone $this->service->pagosDiferidos($request))
            ->has('pagosDiferidos', '>', 1)
            ->get()
            ->map(function ($k, $v){
                return $k->pagosDiferidos->sum('monto');
            });
        $sheet->setCellValue('U8', $num_pagos_parciales->count());

        $sheet->setCellValue('V8', $num_pagos_parciales->sum());



        $num_tot_pagos_parciales = (clone $this->service->pagos($request))
            ->get()->count();
        $sheet->setCellValue('T11', $num_tot_pagos_parciales);


        $num_cumplimientos = (clone $this->service->pagos($request))
            ->where('pagado', true)
            ->get()->count();
        $sheet->setCellValue('T12', $num_cumplimientos);

        $num_incumplimientos = (clone $this->service->pagos($request))
            ->where('pagado', false)->whereNotNull('pagado')
            ->get()->count();
        $sheet->setCellValue('T13', $num_incumplimientos);

        $num_vencidos = (clone $this->service->pagos($request))
            ->whereNull('pagado')
            ->where('fecha_pago', '<', date('Y-m-d'))
            ->get()->count();
        $sheet->setCellValue('T14', $num_vencidos);

        $num_vigentes = (clone $this->service->pagos($request))
            ->whereNull('pagado')
            ->where('fecha_pago', '>=', date('Y-m-d'))
            ->get()->count();
        $sheet->setCellValue('T15', $num_vigentes);


    }

}
