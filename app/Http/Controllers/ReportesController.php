<?php

namespace App\Http\Controllers;


use App\Industria;
use App\ObjetoSolicitud;
use App\Services\ExcelReportesService;
use App\Services\ReportesService;
use App\Traits\EstilosSpreadsheets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportesController extends Controller
{
    use EstilosSpreadsheets;

    /**
     * Centros no implementados en etapa 1 de la reforma
     * @var array
     */
    protected $noImp = ['AGU','BCN','BCS','COA','COL','CHH','CDMX','GUA','GRO','JAL','MIC','MOR','NAY','NLE','OAX','PUE','QUE',
        'ROO','SIN','SON','TAM','TLA','VER','YUC','OCCFCRL',
    ];
    /**
     * Centros implementados en etapa 1
     * @var array
     */
    protected $imp = ['CAM','CAMOAE','CHP','DUR','HID','MEX','SLP','TAB','ZAC'];
    /**
     * Instancia del request
     * @var Request
     */
    protected $request;
    /**
     * ID de tipo de solicitud individual en catálogo de tipos_solicitudes
     */
    const SOLICITUD_INDIVIDUAL = 1;

    /**
     * ID de tipo de solicitud patronal individual en catálogo de tipos_solicitudes
     */
    const SOLICITUD_PATRONAL_INDIVIDUAL = 2;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function index()
    {
        $tipo_objeto_solicitud = [];
        $tipo_objeto_solicitud["1"] = 'Trabajador individual';
        $tipo_objeto_solicitud["2"] = 'Patrón individual';
        $objetos = ObjetoSolicitud::whereIn('tipo_objeto_solicitudes_id',[self::SOLICITUD_INDIVIDUAL, self::SOLICITUD_PATRONAL_INDIVIDUAL])
            ->orderBy('tipo_objeto_solicitudes_id')->orderBy('id')->get();
        $tipoObjetos = [];
        //$tipoObjetos[]=[""=>"--Seleccione"];
        foreach ($objetos as $objeto){
            $tipoObjetos[$objeto->id] = $tipo_objeto_solicitud[$objeto->tipo_objeto_solicitudes_id]." - ".$objeto->nombre;
        }
        $centros = [];
        foreach ($this->imp as $centro){
            $centros[$centro] = $centro;
        }
        $grupo_etario = [];
        $grupo_etario["18-19"] = "De 18 a 19 años";
        $grupo_etario["20-24"] = "De 20 a 24 años";;
        $grupo_etario["25-29"] = "De 25 a 29 años";;
        $grupo_etario["30-34"] = "De 30 a 34 años";;
        $grupo_etario["35-39"] = "De 35 a 39 años";;
        $grupo_etario["40-44"] = "De 40 a 44 años";;
        $grupo_etario["45-49"] = "De 45 a 49 años";;
        $grupo_etario["50-54"] = "De 50 a 54 años";;
        $grupo_etario["55-59"] = "De 55 a 59 años";;
        $grupo_etario["60-64"] = "De 60 a 64 años";;
        $grupo_etario["65-69"] = "De 65 a 69 años";;
        $grupo_etario["70-74"] = "De 70 a 74 años";;
        $grupo_etario["75-79"] = "De 75 a 79 años";;
        $grupo_etario["80-84"] = "De 80 a 84 años";;
        $grupo_etario["85-89"] = "De 85 a 89 años";;

        $tipoIndustria = Industria::orderBy('nombre')->get(['id','nombre'])->pluck('nombre','id');

        return view('reportes.index', compact('tipoObjetos','centros', 'grupo_etario', 'tipoIndustria'));
    }

    public function reporte(ReportesService $reportesService, ExcelReportesService $excelReportesService)
    {
        // Cambiamos a la base de respaldo para las consultas y no pegar en el performance.
        DB::setDefaultConnection('pgsqlqa');

        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);

        $sheet = $spreadsheet->getActiveSheet();

        // SOLICITUDES PRESENTADAS
        $sheet->setTitle('Solicitudes presentadas');
        $solicitudes = $reportesService->solicitudesPresentadas($this->request);

        //dump($this->request->all());
        $excelReportesService->solicitudesPresentadas($sheet, $solicitudes, $this->request);

        // SOLICITUDES CONFIRMADAS
        $solicitudes_confirmadas = $reportesService->solicitudesConfirmadas($this->request);
        $solicitudesPresentadasWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Solicitudes confirmadas');
        $spreadsheet->addSheet($solicitudesPresentadasWorkSheet, 1);
        $excelReportesService->solicitudesConfirmadas($solicitudesPresentadasWorkSheet, $solicitudes_confirmadas, $this->request);

        // CITATORIOS EMITIDOS
        $citatorios = $reportesService->citatoriosEmitidos($this->request);
        $citatoriosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Citatorios');
        $spreadsheet->addSheet($citatoriosWorkSheet, 3);
        $excelReportesService->citatoriosEmitidos($citatoriosWorkSheet, $citatorios, $this->request);

        // INCOMPETENCIAS
        $incompetencias = $reportesService->incompetencias($this->request);
        $incompetenciasWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Incompetencias');
        $spreadsheet->addSheet($incompetenciasWorkSheet, 2);
        $excelReportesService->incompetencias($incompetenciasWorkSheet, $incompetencias, $this->request);

        // ARCHIVADOS POR FALTA DE INTERES
        $archivados = $reportesService->archivadoPorFaltaDeInteres($this->request);
        $archivadosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Archivo X Falta Interés');
        $spreadsheet->addSheet($archivadosWorkSheet, 4);
        $excelReportesService->archivoPorFaltaInteres($archivadosWorkSheet, $archivados, $this->request);

        // CONVENIOS CONCILIACION
        $convenios = $reportesService->conveniosConciliacion($this->request);
        $conveniosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Convenios conciliación');
        $spreadsheet->addSheet($conveniosWorkSheet, 5);
        $excelReportesService->convenios($conveniosWorkSheet, $convenios, $this->request);

        // CONVENIOS RATIFICACIÓN
        $conveniosRatificacion = $reportesService->conveniosRatificacion($this->request);
        $conveniosRatificacionWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Convenios confirmación');
        $spreadsheet->addSheet($conveniosRatificacionWorkSheet, 6);
        $excelReportesService->conveniosRatificacion($conveniosRatificacionWorkSheet, $conveniosRatificacion, $this->request);

        // NO CONCILIACIÓN
        $noConciliacion = $reportesService->noConciliacion($this->request);
        $noConciliacionWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'No conciliación');
        $spreadsheet->addSheet($noConciliacionWorkSheet, 7);
        $excelReportesService->noConciliacion($noConciliacionWorkSheet, $noConciliacion, $this->request);

        // AUDIENCIAS
        $audiencias = $reportesService->audiencias($this->request);
        $audienciasWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Audiencias');
        $spreadsheet->addSheet($audienciasWorkSheet, 8);
        $excelReportesService->audiencias($audienciasWorkSheet, $audiencias, $this->request);

        // PAGOS DIFERIDOS
        $pagosdiferidos = $reportesService->pagosDiferidos($this->request);
        $pagosdiferidosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Pagos diferidos');
        $spreadsheet->addSheet($pagosdiferidosWorkSheet, 9);
        $excelReportesService->pagosDiferidos($pagosdiferidosWorkSheet, $pagosdiferidos, $this->request);

        // Descarga del excel
        $spreadsheet->setActiveSheetIndex(0);
        return $this->descargaExcel($writer);
    }


    public function descargaExcel($writer)
    {
        $response =  new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $nombre_reporte = 'ReporteSINACOL_'.date("Y-m-d_His").".xlsx";
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$nombre_reporte.'"');
        $response->headers->set('Cache-Control','max-age=0');
        return $response;
    }


}
