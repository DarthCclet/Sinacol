<?php

namespace App\Http\Controllers;


use App\Centro;
use App\Conciliador;
use App\Industria;
use App\ObjetoSolicitud;
use App\Services\ExcelReporteOperativoService;
use App\Services\ExcelReportesService;
use App\Services\ReportesService;
use App\Traits\EstilosSpreadsheets;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
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
     * ID del rol Personal conciliador de la tabla roles
     */
    const ROL_PERSONAL_CONCILIADOR = 3;


    public function __construct(Request $request) {
        $this->middleware('can:Reporteador');
        $this->request = $request;
        $this->imp = Centro::whereNotNull('desde')->orderBy('abreviatura')->get()->pluck('abreviatura')->toArray();
    }

    /**
     * Muestra la forma de consulta del reporte
     * @param ReportesService $reportesService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        # Sólo mostramos los objetos para solicitud individual y solicitud patronal en el filtro
        $tipoObjetos = ReportesService::getObjetosFiltrables();
        $tipoObjetosJson = $tipoObjetos->toJson();

        # ToDo: Se debe sacar los centros activos para poder consultar dinámicamente y no de esta lista hardcodeada
        $centros = [];
        foreach ($this->imp as $centro){
            $centros[$centro] = $centro;
        }

        # Los conciliadores
        $conciliadores = $this->listaConciliadores();
        $conciliadoresJson = $conciliadores->toJson();

        # Los grupos etarios
        $grupo_etario = ReportesService::gruposEtarios();

        # Las industrias
        $tipoIndustria = ReportesService::getIndustria();

        # Para loguear los querys
        $querys = $this->request->exists('querys');

        return view('reportes.index', compact('tipoObjetos','centros', 'grupo_etario', 'tipoIndustria', 'tipoObjetosJson', 'conciliadoresJson', 'querys'));
    }

    /**
     * Devuelve el reporte en excel.
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @return StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function reporte(ReportesService $reportesService, ExcelReportesService $excelReportesService)
    {
        // Cambiamos a la base de respaldo para las consultas y no pegar en el performance en producción.
        DB::setDefaultConnection('pgsqlqa');

        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);

        // Si viene el parámetro query logueamos los querys a pantalla
        if($this->request->exists('querys')) DB::enableQueryLog();

        $operaciones = [
            'solicitudesPresentadas',
            'solicitudesConfirmadas',
            'citatoriosEmitidos',
            'incompetencias',
            'archivadoPorFaltaDeIneres',
            'conveniosConciliacion',
            'conveniosRatificacion',
            'noConciliacion',
            'audiencias',
            'pagosDiferidos'
        ];

        foreach($operaciones as $operacion) {
            $this->{$operacion}($reportesService, $excelReportesService, $spreadsheet);
        }

        // Si viene el parámetro query logueamos los querys a pantalla
        if($this->request->exists('querys')) {$res =  ReportesService::debugSql(); dump($res); exit;}

        // Descarga del excel
        $spreadsheet->setActiveSheetIndex(0);

        $tipo = $this->request->get('tipo_reporte');

        $nombre_reporte = 'ReporteSINACOL_'.$tipo.'_'.date("Y-m-d_His").".xlsx";

        return $this->descargaExcel($writer, $nombre_reporte);
    }


    /**
     * Regresa el archivo excel como una StreamedResponse que se descarga al navegador del usuario
     * @param $writer
     * @param $nombre_reporte
     * @return StreamedResponse
     */
    public function descargaExcel($writer, $nombre_reporte)
    {
        $response =  new StreamedResponse(
            function () use ($writer) {
                $writer->setIncludeCharts(true);
                $writer->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$nombre_reporte.'"');
        $response->headers->set('Cache-Control','max-age=0');
        return $response;
    }



    /**
     * Listado de conciliadores activos agrupados por centro
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    protected function listaConciliadores()
    {
        return Conciliador::with('persona', 'centro', 'persona.user.roles')
            ->whereHas(
                'centro',
                function ($q) {
                    $q->whereIn('abreviatura', $this->imp);
                }
            )
            ->whereHas(
                'persona.user.roles',
                function ($q) {
                    $q->where('id', self::ROL_PERSONAL_CONCILIADOR);
                }
            )
            ->get()->map(
                function ($v, $k) {
                    return [
                        'id' => $v->id,
                        'nombre' => trim(
                            mb_strtoupper($v->persona->nombre . " " . $v->persona->primer_apellido . " " . $v->persona->segundo_apellido)
                        ),
                        'centro' => $v->centro->abreviatura
                    ];
                }
            )->groupBy('centro');

    }

    /**
     * Excel proporcionado por personal del CFCRL (Mtra. Gianni)
     * @param ExcelReporteOperativoService $excelReporteOperativoService
     * @return StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function reporteOperativo(ExcelReporteOperativoService $excelReporteOperativoService)
    {
        // Nombre del archivo descargable del reporte
        $nombre_reporte = 'ReporteOperativoSINACOL_'.date("Y-m-d_His").".xlsx";

        // Cambiamos a la base de respaldo para las consultas y no pegar en el performance en producción.
        DB::setDefaultConnection('pgsqlqa');

        // Instancia de la plantilla excel que se va a llenar
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(base_path('database/datafiles/plantilla_reporte_operativo.xlsx'));

        //La primera hoja se va a llamar reporte operativoa
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Reporte operativo');

        // Si viene el parámetro query logueamos los querys a pantalla (debug)
        if($this->request->exists('querys')) DB::enableQueryLog();

        // Generamos el reporte
        $excelReporteOperativoService->reporte($sheet, $this->request);

        // La segunda hoja del archivo se va a llamar indicadoresPorCentro por centro
        $indicadoresPorCentroSheet = $spreadsheet->getSheet(1);

        // Generamos los indicadoresPorCentro
        $excelReporteOperativoService->indicadoresPorCentro($indicadoresPorCentroSheet, $this->request);

        // La terecera hoja del archivo se va a llamar indicadoresPorConciliador
        $indicadoresPorConciliadorSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Indicadores por conciliador');
        $spreadsheet->addSheet($indicadoresPorConciliadorSheet, 2);

        // Generamos los indicadores por conciliador
        $excelReporteOperativoService->indicadoresPorConciliador($indicadoresPorConciliadorSheet, $this->request);

        $spreadsheet->setActiveSheetIndex(0);

        // Si viene el parámetro query logueamos los querys a pantalla
        if($this->request->exists('querys')) {$res =  ReportesService::debugSql(); dump($res); exit;}

        $writer = new Xlsx($spreadsheet);
        return $this->descargaExcel($writer, $nombre_reporte );

    }

    /**
     * SOLICITUDES PRESENTADAS
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     */
    public function solicitudesPresentadas(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Solicitudes presentadas');
        $solicitudes = $reportesService->solicitudesPresentadas($this->request);
        $excelReportesService->solicitudesPresentadas($sheet, $solicitudes, $this->request);
    }

    /**
     * SOLICITUDES CONFIRMADAS
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function solicitudesConfirmadas(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $solicitudes_confirmadas = $reportesService->solicitudesConfirmadas($this->request);
        $solicitudesPresentadasWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet(
            $spreadsheet,
            'Solicitudes confirmadas'
        );
        $spreadsheet->addSheet($solicitudesPresentadasWorkSheet, 1);
        $excelReportesService->solicitudesConfirmadas(
            $solicitudesPresentadasWorkSheet,
            $solicitudes_confirmadas,
            $this->request
        );
    }

    /**
     * CITATORIOS EMITIDOS
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function citatoriosEmitidos(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $citatorios = $reportesService->citatoriosEmitidos($this->request);
        $citatoriosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Citatorios');
        $spreadsheet->addSheet($citatoriosWorkSheet, 3);
        $excelReportesService->citatoriosEmitidos($citatoriosWorkSheet, $citatorios, $this->request);
    }

    /**
     * INCOMPETENCIAS
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function incompetencias(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $incompetencias = $reportesService->incompetencias($this->request);
        $incompetenciasWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Incompetencias');
        $spreadsheet->addSheet($incompetenciasWorkSheet, 2);
        $excelReportesService->incompetencias($incompetenciasWorkSheet, $incompetencias, $this->request);
    }

    /**
     * ARCHIVADOS POR FALTA DE INTERES
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function archivadoPorFaltaDeIneres(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $archivados = $reportesService->archivadoPorFaltaDeInteres($this->request);
        $archivadosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet(
            $spreadsheet, 'Archivo X Falta Interés'
        );
        $spreadsheet->addSheet($archivadosWorkSheet, 4);
        $excelReportesService->archivoPorFaltaInteres($archivadosWorkSheet, $archivados, $this->request);
    }

    /**
     * CONVENIOS CONCILIACION
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function conveniosConciliacion(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $convenios = $reportesService->conveniosConciliacion($this->request);
        $conveniosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Convenios conciliación');
        $spreadsheet->addSheet($conveniosWorkSheet, 5);
        $excelReportesService->convenios($conveniosWorkSheet, $convenios, $this->request);
    }

    /**
     * CONVENIOS RATIFICACIÓN
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function conveniosRatificacion(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $conveniosRatificacion = $reportesService->conveniosRatificacion($this->request);
        $conveniosRatificacionWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet(
            $spreadsheet,
            'Convenios confirmación'
        );
        $spreadsheet->addSheet($conveniosRatificacionWorkSheet, 6);
        $excelReportesService->conveniosRatificacion(
            $conveniosRatificacionWorkSheet,
            $conveniosRatificacion,
            $this->request
        );
    }

    /**
     * NO CONCILIACIÓN
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function noConciliacion(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $noConciliacion = $reportesService->noConciliacion($this->request);
        $noConciliacionWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'No conciliación');
        $spreadsheet->addSheet($noConciliacionWorkSheet, 7);
        $excelReportesService->noConciliacion($noConciliacionWorkSheet, $noConciliacion, $this->request);
    }

    /**
     * AUDIENCIAS
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function audiencias(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $audiencias = $reportesService->audiencias($this->request);
        $audienciasWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Audiencias');
        $spreadsheet->addSheet($audienciasWorkSheet, 8);
        $excelReportesService->audiencias($audienciasWorkSheet, $audiencias, $this->request);
    }

    /**
     * PAGOS DIFERIDOS
     *
     * @param ReportesService $reportesService
     * @param ExcelReportesService $excelReportesService
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function pagosDiferidos(
        ReportesService $reportesService,
        ExcelReportesService $excelReportesService,
        Spreadsheet $spreadsheet
    ): void {
        $pagosdiferidos = $reportesService->pagosDiferidos($this->request);
        $pagosdiferidosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Pagos diferidos');
        $spreadsheet->addSheet($pagosdiferidosWorkSheet, 9);
        $excelReportesService->pagosDiferidos($pagosdiferidosWorkSheet, $pagosdiferidos, $this->request);
    }

}
