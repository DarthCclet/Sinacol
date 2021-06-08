<?php


namespace App\Services;


use App\Traits\EstilosSpreadsheets;
use Illuminate\Support\Facades\Log;
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

        $pppatr = ($this->service->solicitudes($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL);
        $sheet->setCellValue('B9', $pppatr->count());
        $sheet->setCellValue('C9', $pppatr->where('ratificada',true)->count());
        $sheet->setCellValue('D9', ($this->service->solicitudes($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL)->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E9', ($this->service->solicitudes($request))->where('tipo_solicitud_id', ReportesService::SOLICITUD_PATRONAL_INDIVIDUAL)->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());

        # con asistencia de personal o por los usuarios
        $caspc = ($this->service->solicitudes($request))->where('user_id', '>', 1);
        $sheet->setCellValue('B12', $caspc->count());
        $sheet->setCellValue('C12', $caspc->where('ratificada', true)->count());
        $sheet->setCellValue('D12', ($this->service->solicitudes($request))->where('user_id', '>', 1)->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E12', ($this->service->solicitudes($request))->where('user_id', '>', 1)->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());

        $sol = ($this->service->solicitudes($request))->whereRaw('(user_id = 1 or user_id is null)');
        $sheet->setCellValue('B13', $sol->count());
        $sheet->setCellValue('C13', $sol->where('ratificada', true)->count());
        $sheet->setCellValue('D13', ($this->service->solicitudes($request))->whereRaw('(user_id = 1 or user_id is null)')->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  <= CURRENT_DATE" )->count());
        $sheet->setCellValue('E13', ($this->service->solicitudes($request))->whereRaw('(user_id = 1 or user_id is null)')->where('ratificada', false)->whereRaw("(created_at::date + '". self::DIAS_PARA_ARCHIVAR." days'::interval)::date  > CURRENT_DATE" )->count());

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

    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////

}
