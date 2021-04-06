<?php

namespace App\Http\Controllers;


use App\ObjetoSolicitud;
use App\Services\ReportesService;
use App\Traits\EstilosSpreadsheets;
use Illuminate\Http\Request;
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

        return view('reportes.index', compact('tipoObjetos','centros', 'grupo_etario'));
    }

    public function reporte(ReportesService $reportesService)
    {
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Solicitudes presentadas');

        // SOLICITUDES PRESENTADAS
        $solicitudes = $reportesService->solicitudesPresentadas($this->request);

        $sheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $sheet->getStyle('A3:B3')->applyFromArray($this->th1());
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $sheet->setCellValue('A1', 'SOLICITUDES PRESENTADAS');
        $sheet->setCellValue('A3', 'CENTRO');
        $sheet->setCellValue('B3', 'PRESENTADAS');
        $c = 4;
        foreach ($solicitudes->toArray() as $centro => $cantidad){
            if (in_array($centro, $this->noImp)) continue;
            $sheet->setCellValue('A'.$c, $centro);
            $sheet->setCellValue('B'.$c, $cantidad);
            $c++;
        }
        $sheet->setCellValue('A'.$c, 'Total');
        $sheet->setCellValue('B'.$c, "=SUM(B3:B$c)")
            ->getStyle('B'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $sheet->getStyle('A3:B'.$c)->applyFromArray($this->tbody());
        $sheet->getStyle('A'.$c.':B'.$c)->applyFromArray($this->tf1());


        // SOLICITUDES CONFIRMADAS
        list($inmediata, $normal) = $reportesService->solicitudesConfirmadas($this->request);

        $solicitudesPresentadasWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Solicitudes confirmadas');
        $spreadsheet->addSheet($solicitudesPresentadasWorkSheet, 1);
        $solicitudesPresentadasWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $solicitudesPresentadasWorkSheet->getStyle('A3:D3')->applyFromArray($this->th1());
        $solicitudesPresentadasWorkSheet->getColumnDimension('B')->setAutoSize(true);
        $solicitudesPresentadasWorkSheet->getColumnDimension('C')->setAutoSize(true);
        $solicitudesPresentadasWorkSheet->getColumnDimension('D')->setAutoSize(true);

        $solicitudesPresentadasWorkSheet->setCellValue('A1', 'SOLICITUDES CONFIRMADAS');

        $solicitudesPresentadasWorkSheet->setCellValue('A3', 'CENTRO');
        $solicitudesPresentadasWorkSheet->setCellValue('B3', 'Ratificación de convenio');
        $solicitudesPresentadasWorkSheet->setCellValue('C3', 'Procedimiento normal');
        $solicitudesPresentadasWorkSheet->setCellValue('D3', 'Total');

        $c = 4;
        foreach ($this->imp as $centro){
            $solicitudesPresentadasWorkSheet->setCellValue('A'.$c, $centro);
            $solicitudesPresentadasWorkSheet->setCellValue('B' . $c, isset($inmediata[$centro]) ? count($inmediata[$centro]) : 0);
            $solicitudesPresentadasWorkSheet->setCellValue('C' . $c, isset($normal[$centro]) ? count($normal[$centro]) : 0);
            $solicitudesPresentadasWorkSheet->setCellValue('D' . $c, "=SUM(B$c:C$c)");
            $c++;
        }
        $solicitudesPresentadasWorkSheet->setCellValue('A'.$c, 'Total');
        $solicitudesPresentadasWorkSheet->setCellValue('B'.$c, "=SUM(B4:B$c)")
            ->getStyle('B'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $solicitudesPresentadasWorkSheet->setCellValue('C'.$c, "=SUM(C4:C$c)")
            ->getStyle('C'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $solicitudesPresentadasWorkSheet->setCellValue('D'.$c, "=SUM(D4:D$c)")
            ->getStyle('D'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');


        // CITATORIOS EMITIDOS
        $citatorios = $reportesService->citatoriosEmitidos($this->request);

        $citatoriosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Citatorios');
        $spreadsheet->addSheet($citatoriosWorkSheet, 2);
        $citatoriosWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $citatoriosWorkSheet->getStyle('F2')->applyFromArray($this->boldcenter());
        $citatoriosWorkSheet->getStyle('A3:I3')->applyFromArray($this->th1());
        $citatoriosWorkSheet->getColumnDimension('B')->setAutoSize(true);
        $citatoriosWorkSheet->getColumnDimension('C')->setAutoSize(true);
        $citatoriosWorkSheet->getColumnDimension('D')->setAutoSize(true);
        $citatoriosWorkSheet->getColumnDimension('E')->setAutoSize(true);
        $citatoriosWorkSheet->getColumnDimension('F')->setAutoSize(true);
        $citatoriosWorkSheet->getColumnDimension('G')->setAutoSize(true);
        $citatoriosWorkSheet->getColumnDimension('H')->setAutoSize(true);
        $citatoriosWorkSheet->getColumnDimension('I')->setAutoSize(true);

        $citatoriosWorkSheet->setCellValue('A1', 'CITATORIOS EMITIDOS');

        $citatoriosWorkSheet->setCellValue('A3', 'CENTRO');
        $citatoriosWorkSheet->setCellValue('B3', 'Entrega solicitante');
        $citatoriosWorkSheet->setCellValue('C3', 'Entrega notificador');
        $citatoriosWorkSheet->setCellValue('D3', 'Cita con notificador');
        $citatoriosWorkSheet->setCellValue('E3', 'Total Citatorios');
        $citatoriosWorkSheet->setCellValue('F3', '1as audiencias');
        $citatoriosWorkSheet->setCellValue('G3', '2as audiencias');
        $citatoriosWorkSheet->setCellValue('H3', '3as audiencias');
        $citatoriosWorkSheet->setCellValue('I3', 'Total audiencias');
        $citatoriosWorkSheet->mergeCells('F2:I2');
        $citatoriosWorkSheet->setCellValue('F2', 'Número de audiencias para las que se emitió citatorio');
        $c = 4;
        foreach ($this->imp as $centro){
            $citatoriosWorkSheet->setCellValue('A'.$c, $centro);
            $citatoriosWorkSheet->setCellValue('B' . $c, isset($citatorios['entrega_solicitante'][$centro]) ? $citatorios['entrega_solicitante'][$centro] : 0);
            $citatoriosWorkSheet->setCellValue('C' . $c, isset($citatorios['entrega_notificador'][$centro]) ? $citatorios['entrega_notificador'][$centro] : 0);
            $citatoriosWorkSheet->setCellValue('D' . $c, isset($citatorios['entrega_notificador_cita'][$centro]) ? $citatorios['entrega_notificador_cita'][$centro] : 0);
            $citatoriosWorkSheet->setCellValue('E' . $c, "=SUM(B$c:D$c)");

            $citatoriosWorkSheet->setCellValue('F' . $c, isset($citatorios['citatorio_en_primera_audiencia'][$centro]) ? $citatorios['citatorio_en_primera_audiencia'][$centro] : 0);
            $citatoriosWorkSheet->setCellValue('G' . $c, isset($citatorios['citatorio_en_segunda_audiencia'][$centro]) ? $citatorios['citatorio_en_segunda_audiencia'][$centro] : 0);
            $citatoriosWorkSheet->setCellValue('H' . $c, isset($citatorios['citatorio_en_tercera_audiencia'][$centro]) ? $citatorios['citatorio_en_tercera_audiencia'][$centro] : 0);

            $citatoriosWorkSheet->setCellValue('I' . $c, "=SUM(F$c:H$c)");

            $c++;
        }
        $citatoriosWorkSheet->setCellValue('A'.$c, 'Total');
        $citatoriosWorkSheet->setCellValue('B'.$c, "=SUM(B4:B$c)")
            ->getStyle('B'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $citatoriosWorkSheet->setCellValue('C'.$c, "=SUM(C4:C$c)")
            ->getStyle('C'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $citatoriosWorkSheet->setCellValue('D'.$c, "=SUM(D4:D$c)")
            ->getStyle('D'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $citatoriosWorkSheet->setCellValue('E'.$c, "=SUM(E4:E$c)")
            ->getStyle('E'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $citatoriosWorkSheet->setCellValue('F'.$c, "=SUM(F4:F$c)")
            ->getStyle('F'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $citatoriosWorkSheet->setCellValue('G'.$c, "=SUM(G4:G$c)")
            ->getStyle('G'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $citatoriosWorkSheet->setCellValue('H'.$c, "=SUM(H4:H$c)")
            ->getStyle('H'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $citatoriosWorkSheet->setCellValue('I'.$c, "=SUM(I4:I$c)")
            ->getStyle('I'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');


        // INCOMPETENCIAS
        $incompetencias = $reportesService->incompetencias($this->request);

        $incompetenciasWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Incompetencias');
        $spreadsheet->addSheet($incompetenciasWorkSheet, 3);

        $incompetenciasWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $incompetenciasWorkSheet->getStyle('A3:D3')->applyFromArray($this->th1());
        $incompetenciasWorkSheet->getColumnDimension('B')->setAutoSize(true);
        $incompetenciasWorkSheet->getColumnDimension('C')->setAutoSize(true);
        $incompetenciasWorkSheet->getColumnDimension('D')->setAutoSize(true);

        $incompetenciasWorkSheet->setCellValue('A1', 'INCOMPETENCIAS');
        $incompetenciasWorkSheet->setCellValue('A3', 'CENTRO');
        $incompetenciasWorkSheet->setCellValue('B3', 'INCOMPETENCIA');
        $incompetenciasWorkSheet->setCellValue('C3', 'DETECTADA EN AUDIENCIA');
        $incompetenciasWorkSheet->setCellValue('D3', 'TOTAL');

        $c = 4;
        foreach ($this->imp as $centro){
            $incompetenciasWorkSheet->setCellValue('A'.$c, $centro);
            $incompetenciasWorkSheet->setCellValue(
                'B' . $c,
                isset($incompetencias['en_ratificacion'][$centro]) ? $incompetencias['en_ratificacion'][$centro] : 0
            );
            $incompetenciasWorkSheet->setCellValue(
                'C' . $c,
                isset($incompetencias['en_audiencia'][$centro]) ? $incompetencias['en_audiencia'][$centro] : 0
            );
            $incompetenciasWorkSheet->setCellValue(
                'D' . $c,
                "=SUM(B$c:C$c)"
            );
            $c++;
        }
        $incompetenciasWorkSheet->setCellValue('A'.$c, 'Total');
        $incompetenciasWorkSheet->setCellValue('B'.$c, "=SUM(B3:B$c)")
            ->getStyle('B'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $incompetenciasWorkSheet->setCellValue('C'.$c, "=SUM(C3:C$c)")
            ->getStyle('C'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $incompetenciasWorkSheet->setCellValue('D'.$c, "=SUM(D3:D$c)")
            ->getStyle('D'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');


        // ARCHIVADOS POR FALTA DE INTERES
        $archivados = $reportesService->archivadoPorFaltaDeInteres($this->request);
        $archivadosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Archivo X Falta Interés');
        $spreadsheet->addSheet($archivadosWorkSheet, 4);

        $archivadosWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $archivadosWorkSheet->getStyle('A3:B3')->applyFromArray($this->th1());
        $archivadosWorkSheet->getColumnDimension('B')->setAutoSize(true);

        $archivadosWorkSheet->setCellValue('A1', 'ARCHIVO POR FALTA DE INTERÉS');
        $archivadosWorkSheet->setCellValue('A3', 'CENTRO');
        $archivadosWorkSheet->setCellValue('B3', 'SOLICITUDES');

        $c = 4;
        foreach ($this->imp as $centro){
            $archivadosWorkSheet->setCellValue('A'.$c, $centro);
            $archivadosWorkSheet->setCellValue(
                'B' . $c,
                isset($archivados[$centro]) ? $archivados[$centro] : 0
            );
            $c++;
        }
        $archivadosWorkSheet->setCellValue('A'.$c, 'Total');
        $archivadosWorkSheet->setCellValue('B'.$c, "=SUM(B3:B$c)")
            ->getStyle('B'.$c)->getNumberFormat()
            ->setFormatCode('#,##0');


        // CONVENIOS CONCILIACION
        $convenios = $reportesService->conveniosConciliacion($this->request);

        $conveniosWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Convenios conciliación');
        $spreadsheet->addSheet($conveniosWorkSheet, 5);

        $conveniosWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $conveniosWorkSheet->getStyle('A3:C3')->applyFromArray($this->th1());
        $conveniosWorkSheet->getColumnDimension('B')->setAutoSize(true);
        $conveniosWorkSheet->getColumnDimension('C')->setAutoSize(true);

        $conveniosWorkSheet->setCellValue('A1', 'CONVENIOS');
        $conveniosWorkSheet->setCellValue('A3', 'CENTRO');
        $conveniosWorkSheet->setCellValue('B3', 'SOLICITUDES');
        $conveniosWorkSheet->setCellValue('C3', 'IMPORTES');

        $c = 4;
        foreach ($this->imp as $centro){
            $conveniosWorkSheet->setCellValue('A'.$c, $centro);
            //TODO: completar este dato
            $conveniosWorkSheet->setCellValue('B'.$c, 0);
            $conveniosWorkSheet->setCellValue(
                'C' . $c,
                isset($convenios[$centro]) ? $convenios[$centro] : 0
            );
            $c++;
        }
        $conveniosWorkSheet->setCellValue('A'.$c, 'Total');
        $conveniosWorkSheet->setCellValue('B'.$c, "=SUM(B3:B$c)")
            ->getStyle('B'.$c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $conveniosWorkSheet->setCellValue('C'.$c, "=SUM(C3:C$c)")
            ->getStyle('C'.$c)->getNumberFormat()
            ->setFormatCode('#,##0.00');









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
