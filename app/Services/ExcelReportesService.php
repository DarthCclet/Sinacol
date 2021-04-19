<?php


namespace App\Services;


use App\Traits\EstilosSpreadsheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReportesService
{
    use EstilosSpreadsheets;


    /**
     * Centros no implementados en etapa 1 de la reforma
     * @var array
     */
    protected $noImp = [
        'AGU',
        'BCN',
        'BCS',
        'COA',
        'COL',
        'CHH',
        'CDMX',
        'GUA',
        'GRO',
        'JAL',
        'MIC',
        'MOR',
        'NAY',
        'NLE',
        'OAX',
        'PUE',
        'QUE',
        'ROO',
        'SIN',
        'SON',
        'TAM',
        'TLA',
        'VER',
        'YUC',
        'OCCFCRL',
    ];

    /**
     * Centros implementados en etapa 1
     * @var array
     */
    protected $imp = ['CAM', 'CAMOAE', 'CHP', 'DUR', 'HID', 'MEX', 'SLP', 'TAB', 'ZAC'];

    /**
     * Construye la hoja de solicitudes presentadas
     * @param Worksheet $sheet
     * @param $solicitudes
     * @param $request
     */
    public function solicitudesPresentadas(Worksheet $sheet, $solicitudes, $request): void
    {
        if ($request->get('tipo_reporte') == 'agregado') {
            # Seteo de encabezados
            $sheet->getStyle('A1')->applyFromArray($this->tituloH1());
            $sheet->getStyle('A3:B3')->applyFromArray($this->th1());
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->setCellValue('A1', 'SOLICITUDES PRESENTADAS');
            $this->arrayToExcel([['CENTRO', 'PRESENTADAS']], $sheet, 3);

            $c = 4;
            # Procesamiento de los datos obtenidos, sólo se extraen la cantidad y la abreviatura
            # Si no existen en los centros de la primera etapa no se toman en cuenta los resultados por consiederarse outliers
            foreach ($solicitudes->pluck('count', 'abreviatura')->toArray() as $centro => $cantidad) {
                if (in_array($centro, $this->noImp)) {
                    continue;
                }
                $sheet->setCellValue('A' . $c, $centro);
                $sheet->setCellValue('B' . $c, $cantidad);
                $c++;
            }

            # Se agrega fórmula de totales al pie de la tabla
            $sheet->setCellValue('A' . $c, 'Total');
            $sheet->setCellValue('B' . $c, "=SUM(B3:B$c)")
                ->getStyle('B' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->getStyle('A3:B' . $c)->applyFromArray($this->tbody());
            $sheet->getStyle('A' . $c . ':B' . $c)->applyFromArray($this->tf1());

            return;
        }

        // Los registros desagregados correspondientes a la consulta
        $encabezado = [
            'CENTRO',
            'FOLIO',
            'AÑO',
            'FECHA RECEPCIÓN',
            'FECHA CONFIRMACIÓN',
            'FECHA CONFLICTO',
            'INMEDIATA',
            'TIPO SOLICITUD',
            'OBJETO SOLICITUD',
            'INDUSTRIA (SCIAN)',
            'CÓDIGO (SCIAN)',
            'ID'
        ];

        # Seteo de encabezados
        $sheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $sheet->getStyle('A3:Z3')->applyFromArray($this->th1());
        $sheet->setCellValue('A1', 'SOLICITUDES PRESENTADAS (DESAGREGADO)');
        foreach ($this->excelColumnRange(count($encabezado) - 1, 'B') as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }
        $this->arrayToExcel([$encabezado], $sheet, 3);

        # Procesamos los datos
        //dd($solicitudes);
        $s = $solicitudes->reject(
            function ($valor, $llave) {
                # Rechazamos cualquier dato no previsto en la primera etapa
                return in_array($valor->abreviatura, $this->noImp);
            }
        )->map(
            function ($v, $k) {
                # Extraemos los valores de los datos que vamos a poner en las columnas desagregadas únicamente
                $objeto = null;
                $objeto_solicitud = isset($v->objeto_solicitudes) ? $v->objeto_solicitudes->implode('nombre', ', ') : null;
                $industria = isset($v->giroComercial) ? $v->giroComercial->nombre : null;
                $codigo_scian = isset($v->giroComercial) ? $v->giroComercial->codigo : null;
                return [
                    'abreviatura' => $v->abreviatura,
                    'folio' => $v->folio,
                    'anio' => $v->anio,
                    'fecha_recepcion' => $v->fecha_recepcion,
                    'fecha_confirmacion' => $v->fecha_ratificacion,
                    'fecha_conflicto' => $v->fecha_conflicto,
                    'inmediata' => $v->inmediata,
                    'tipo_solicitud' => isset($v->tipoSolicitud->nombre) ? $v->tipoSolicitud->nombre : null,
                    'objeto_solicitud' => $objeto_solicitud,
                    'industria' => $industria,
                    'codigo_scian' => $codigo_scian,
                    'id' => $v->sid
                ];
            }
        );
        //dump($s->toArray());
        //dd($s->toArray());
        $this->arrayToExcel($s->toArray(), $sheet, 4);
    }

    /**
     * Las solicitudes confirmadas
     * @param $sheet
     * @param $solicitudes
     * @param $request
     */
    public function solicitudesConfirmadas($sheet, $solicitudes, $request)
    {
        if ($request->get('tipo_reporte') == 'agregado') {

            list($inmediata, $normal) = $solicitudes;

            $sheet->getStyle('A1')->applyFromArray($this->tituloH1());
            $sheet->getStyle('A3:D3')->applyFromArray($this->th1());
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);

            $sheet->setCellValue('A1', 'SOLICITUDES CONFIRMADAS');

            $sheet->setCellValue('A3', 'CENTRO');
            $sheet->setCellValue('B3', 'Ratificación de convenio');
            $sheet->setCellValue('C3', 'Procedimiento normal');
            $sheet->setCellValue('D3', 'Total');

            $c = 4;
            foreach ($this->imp as $centro) {
                $sheet->setCellValue('A' . $c, $centro);
                $sheet->setCellValue('B' . $c,
                    isset($inmediata[$centro]) ? count(
                        $inmediata[$centro]
                    ) : 0
                );
                $sheet->setCellValue(
                    'C' . $c,
                    isset($normal[$centro]) ? count($normal[$centro]) : 0
                );
                $sheet->setCellValue('D' . $c, "=SUM(B$c:C$c)");
                $c++;
            }
            $sheet->setCellValue('A' . $c, 'Total');
            $sheet->setCellValue('B' . $c, "=SUM(B4:B$c)")
                ->getStyle('B' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->setCellValue('C' . $c, "=SUM(C4:C$c)")
                ->getStyle('C' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $sheet->setCellValue('D' . $c, "=SUM(D4:D$c)")
                ->getStyle('D' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            return;
        }

        // Los registros desagregados correspondientes a la consulta
        $encabezado = [
            'CENTRO',
            'FOLIO',
            'AÑO',
            'FECHA RECEPCIÓN',
            'FECHA CONFIRMACIÓN',
            'FECHA CONFLICTO',
            'INMEDIATA',
            'TIPO SOLICITUD',
            'OBJETO SOLICITUD',
            'INDUSTRIA (SCIAN)',
            'CÓDIGO (SCIAN)',
            'ID'
        ];

        # Seteo de encabezados
        $sheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $sheet->getStyle('A3:Z3')->applyFromArray($this->th1());
        $sheet->setCellValue('A1', 'SOLICITUDES CONFIRMADAS (DESAGREGADO)');
        foreach ($this->excelColumnRange(count($encabezado) - 1, 'B') as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }
        $this->arrayToExcel([$encabezado], $sheet, 3);

        # Procesamos los datos
        //dd($solicitudes);
        $s = $solicitudes->reject(
            function ($valor, $llave) {
                # Rechazamos cualquier dato no previsto en la primera etapa
                return in_array($valor->abreviatura, $this->noImp);
            }
        )->map(
            function ($v, $k) {
                # Extraemos los valores de los datos que vamos a poner en las columnas desagregadas únicamente
                $objeto = null;
                $objeto_solicitud = isset($v->objeto_solicitudes) ? $v->objeto_solicitudes->implode('nombre', ', ') : null;
                $industria = isset($v->giroComercial) ? $v->giroComercial->nombre : null;
                $codigo_scian = isset($v->giroComercial) ? $v->giroComercial->codigo : null;
                return [
                    'abreviatura' => $v->abreviatura,
                    'folio' => $v->folio,
                    'anio' => $v->anio,
                    'fecha_recepcion' => $v->fecha_recepcion,
                    'fecha_confirmacion' => $v->fecha_ratificacion,
                    'fecha_conflicto' => $v->fecha_conflicto,
                    'inmediata' => $v->inmediata,
                    'tipo_solicitud' => isset($v->tipoSolicitud->nombre) ? $v->tipoSolicitud->nombre : null,
                    'objeto_solicitud' => $objeto_solicitud,
                    'industria' => $industria,
                    'codigo_scian' => $codigo_scian,
                    'id' => $v->sid
                ];
            }
        );
        $this->arrayToExcel($s->toArray(), $sheet, 4);
    }

    /**
     * Los citatorios emitidos
     * @param $citatoriosWorkSheet
     * @param $citatorios
     * @param $request
     */
    public function citatoriosEmitidos($citatoriosWorkSheet, $citatorios, $request)
    {
        if ($request->get('tipo_reporte') == 'agregado') {
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
            foreach ($this->imp as $centro) {
                $citatoriosWorkSheet->setCellValue('A' . $c, $centro);
                $citatoriosWorkSheet->setCellValue(
                    'B' . $c,
                    isset($citatorios['entrega_solicitante'][$centro]) ? $citatorios['entrega_solicitante'][$centro] : 0
                );
                $citatoriosWorkSheet->setCellValue(
                    'C' . $c,
                    isset($citatorios['entrega_notificador'][$centro]) ? $citatorios['entrega_notificador'][$centro] : 0
                );
                $citatoriosWorkSheet->setCellValue(
                    'D' . $c,
                    isset($citatorios['entrega_notificador_cita'][$centro]) ? $citatorios['entrega_notificador_cita'][$centro] : 0
                );
                $citatoriosWorkSheet->setCellValue('E' . $c, "=SUM(B$c:D$c)");

                $citatoriosWorkSheet->setCellValue(
                    'F' . $c,
                    isset($citatorios['citatorio_en_primera_audiencia'][$centro]) ? $citatorios['citatorio_en_primera_audiencia'][$centro] : 0
                );
                $citatoriosWorkSheet->setCellValue(
                    'G' . $c,
                    isset($citatorios['citatorio_en_segunda_audiencia'][$centro]) ? $citatorios['citatorio_en_segunda_audiencia'][$centro] : 0
                );
                $citatoriosWorkSheet->setCellValue(
                    'H' . $c,
                    isset($citatorios['citatorio_en_tercera_audiencia'][$centro]) ? $citatorios['citatorio_en_tercera_audiencia'][$centro] : 0
                );

                $citatoriosWorkSheet->setCellValue('I' . $c, "=SUM(F$c:H$c)");

                $c++;
            }
            $citatoriosWorkSheet->setCellValue('A' . $c, 'Total');
            $citatoriosWorkSheet->setCellValue('B' . $c, "=SUM(B4:B$c)")
                ->getStyle('B' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $citatoriosWorkSheet->setCellValue('C' . $c, "=SUM(C4:C$c)")
                ->getStyle('C' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $citatoriosWorkSheet->setCellValue('D' . $c, "=SUM(D4:D$c)")
                ->getStyle('D' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $citatoriosWorkSheet->setCellValue('E' . $c, "=SUM(E4:E$c)")
                ->getStyle('E' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $citatoriosWorkSheet->setCellValue('F' . $c, "=SUM(F4:F$c)")
                ->getStyle('F' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $citatoriosWorkSheet->setCellValue('G' . $c, "=SUM(G4:G$c)")
                ->getStyle('G' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $citatoriosWorkSheet->setCellValue('H' . $c, "=SUM(H4:H$c)")
                ->getStyle('H' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $citatoriosWorkSheet->setCellValue('I' . $c, "=SUM(I4:I$c)")
                ->getStyle('I' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');

            return;
        }

        # Para desagregados

        $citatoriosWorkSheet->setCellValue('A1', 'CITATORIOS EMITIDOS (DESAGREGADO)');

        // Los registros desagregados correspondientes a la consulta
        $encabezado = [
            'CENTRO',
            'FOLIO AUDIENCIA',
            'AÑO AUDIENCIA',
            'TIPO CITATORIO',
            '# AUDIENCIA',
            'FECHA CITATORIO',
            'EXPEDIENTE',
            'AUDIENCIA ID',
            'SOLICITUD ID',
            'PARTE ID',
        ];

        # Seteo de encabezados
        $tipos_notificaciones = ['1'=>'Entrega Solicitante', '2'=>'Entrega notificador', '3'=>'Cita con notificador'];
        foreach ($this->excelColumnRange(count($encabezado) - 1, 'B') as $columna) {
            $citatoriosWorkSheet->getColumnDimension($columna)->setAutoSize(true);
        }
        $this->arrayToExcel([$encabezado], $citatoriosWorkSheet, 3);

        $s = $citatorios->reject(
            function ($valor, $llave) {
                # Rechazamos cualquier dato no previsto en la primera etapa
                return in_array($valor->abreviatura, $this->noImp);
            }
        )->map(
            function ($v, $k) use($tipos_notificaciones) {
                # Extraemos los valores de los datos que vamos a poner en las columnas desagregadas únicamente
                return [
                    'abreviatura' => $v->abreviatura,
                    'folio_audiencia' => $v->folio,
                    'anio_audiencia' => $v->anio,
                    'tipo_citatorio' => isset($tipos_notificaciones[$v->tipo_notificacion_id])?$tipos_notificaciones[$v->tipo_notificacion_id]:null,
                    'num_audiencia' => $v->numero_audiencia,
                    'fecha_citatorio' => $v->fecha_citatorio,
                    'expediente' => $v->expediente_folio,
                    'audiencia_id' => $v->audiencia_id,
                    'solicitud_id' => $v->solicitud_id,
                    'parte_id' => $v->parte_id,
                ];
            }
        );
        $this->arrayToExcel($s->toArray(), $citatoriosWorkSheet, 4);
    }


    /**
     * Las incompetencias declaradas
     * @param $incompetenciasWorkSheet
     * @param $incompetencias
     * @param $request
     */
    public function incompetencias($incompetenciasWorkSheet, $incompetencias, $request)
    {
        // INCOMPETENCIAS
        if ($request->get('tipo_reporte') == 'agregado') {
            $incompetenciasWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
            $incompetenciasWorkSheet->getStyle('A3:D3')->applyFromArray($this->th1());
            $incompetenciasWorkSheet->getColumnDimension('B')->setAutoSize(true);
            $incompetenciasWorkSheet->getColumnDimension('C')->setAutoSize(true);
            $incompetenciasWorkSheet->getColumnDimension('D')->setAutoSize(true);

            $incompetenciasWorkSheet->setCellValue('A1', 'INCOMPETENCIAS');

            $encabezado = explode(',', "CENTRO,INCOMPETENCIA,DETECTADA EN AUDIENCIA,TOTAL");

            $incompetenciasWorkSheet->setCellValue('A3', 'CENTRO');
            $incompetenciasWorkSheet->setCellValue('B3', 'INCOMPETENCIA');
            $incompetenciasWorkSheet->setCellValue('C3', 'DETECTADA EN AUDIENCIA');
            $incompetenciasWorkSheet->setCellValue('D3', 'TOTAL');


            $c = 4;
            foreach ($this->imp as $centro) {
                $incompetenciasWorkSheet->setCellValue('A' . $c, $centro);
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

            $incompetenciasWorkSheet->setCellValue('A' . $c, 'Total');
            $incompetenciasWorkSheet->setCellValue('B' . $c, "=SUM(B3:B$c)")
                ->getStyle('B' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $incompetenciasWorkSheet->setCellValue('C' . $c, "=SUM(C3:C$c)")
                ->getStyle('C' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            $incompetenciasWorkSheet->setCellValue('D' . $c, "=SUM(D3:D$c)")
                ->getStyle('D' . $c)->getNumberFormat()
                ->setFormatCode('#,##0');
            return;
        }

        $encabezado = explode(',', "CENTRO,INCOMPETENCIA,DETECTADA EN AUDIENCIA,TOTAL");

    }

    /**
     * Archivados por falta de interés
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $archivadosWorkSheet
     * @param $archivados
     * @param $request
     */
    public function archivoPorFaltaInteres(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $archivadosWorkSheet,
        $archivados, $request
    ): void {

        $archivadosWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $archivadosWorkSheet->getStyle('A3:B3')->applyFromArray($this->th1());
        $archivadosWorkSheet->getColumnDimension('B')->setAutoSize(true);

        $archivadosWorkSheet->setCellValue('A1', 'ARCHIVO POR FALTA DE INTERÉS');
        $archivadosWorkSheet->setCellValue('A3', 'CENTRO');
        $archivadosWorkSheet->setCellValue('B3', 'SOLICITUDES');

        $c = 4;
        foreach ($this->imp as $centro) {
            $archivadosWorkSheet->setCellValue('A' . $c, $centro);
            $archivadosWorkSheet->setCellValue(
                'B' . $c,
                isset($archivados[$centro]) ? $archivados[$centro] : 0
            );
            $c++;
        }
        $archivadosWorkSheet->setCellValue('A' . $c, 'Total');
        $archivadosWorkSheet->setCellValue('B' . $c, "=SUM(B3:B$c)")
            ->getStyle('B' . $c)->getNumberFormat()
            ->setFormatCode('#,##0');


    }

    /**
     * Convenios de conciliación
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $conveniosWorkSheet
     * @param $convenios
     * @param $request
     */
    public function convenios(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $conveniosWorkSheet, $convenios, $request): void
    {
        $conveniosWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $conveniosWorkSheet->getStyle('A3:C3')->applyFromArray($this->th1());
        $conveniosWorkSheet->getColumnDimension('B')->setAutoSize(true);
        $conveniosWorkSheet->getColumnDimension('C')->setAutoSize(true);

        $conveniosWorkSheet->setCellValue('A1', 'CONVENIOS');
        $conveniosWorkSheet->setCellValue('A3', 'CENTRO');
        $conveniosWorkSheet->setCellValue('B3', 'SOLICITUDES');
        $conveniosWorkSheet->setCellValue('C3', 'IMPORTES');

        $c = 4;
        foreach ($this->imp as $centro) {
            $conveniosWorkSheet->setCellValue('A' . $c, $centro);
            //TODO: completar este dato
            $conveniosWorkSheet->setCellValue('B' . $c, 0);
            $conveniosWorkSheet->setCellValue(
                'C' . $c,
                isset($convenios[$centro]) ? $convenios[$centro] : 0
            );
            $c++;
        }
        $conveniosWorkSheet->setCellValue('A' . $c, 'Total');
        $conveniosWorkSheet->setCellValue('B' . $c, "=SUM(B3:B$c)")
            ->getStyle('B' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $conveniosWorkSheet->setCellValue('C' . $c, "=SUM(C3:C$c)")
            ->getStyle('C' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
    }

    /**
     * Convenios con ratificación (inmediatos)
     * @param Worksheet $conveniosWorkSheet
     * @param $convenios
     * @param $request
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function conveniosRatificacion(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $conveniosWorkSheet, $convenios, $request): void
    {
        $conveniosWorkSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $conveniosWorkSheet->getStyle('A4:H4')->applyFromArray($this->th1());
        $conveniosWorkSheet->getStyle('C3:G3')->applyFromArray($this->boldcenter());
        $conveniosWorkSheet->getColumnDimension('B')->setAutoSize(true);
        $conveniosWorkSheet->getColumnDimension('C')->setAutoSize(true);

        $conveniosWorkSheet->mergeCells('C3:E3');
        $conveniosWorkSheet->setCellValue('C3', 'CONCLUIDAS');
        $conveniosWorkSheet->mergeCells('G3:H3');
        $conveniosWorkSheet->setCellValue('G3', 'SIN CONCLUIR');

        $conveniosWorkSheet->setCellValue('A1', 'RATIFICACIÓN DE CONVENIOS');
        $conveniosWorkSheet->setCellValue('A4', 'Centro');
        $conveniosWorkSheet->setCellValue('B4', 'Solicitudes');
        $conveniosWorkSheet->setCellValue('C4', 'Hubo convenio');
        $conveniosWorkSheet->setCellValue('D4', 'Importe convenio');
        $conveniosWorkSheet->setCellValue('E4', 'Archivado');
        $conveniosWorkSheet->setCellValue('F4', 'Sin resolución');
        $conveniosWorkSheet->setCellValue('G4', 'No hubo convenio');
        $conveniosWorkSheet->setCellValue('H4', 'Sin resolución');

        $c = 5;
        foreach ($this->imp as $centro) {
            $conveniosWorkSheet->setCellValue('A' . $c, $centro);
            //TODO: completar este dato
            $conveniosWorkSheet->setCellValue('B' . $c, 0);
            $conveniosWorkSheet->setCellValue(
                'C' . $c,
                isset($convenios[$centro]) ? $convenios[$centro] : 0
            );
            $c++;
        }
        $conveniosWorkSheet->setCellValue('A' . $c, 'Total');
        $conveniosWorkSheet->setCellValue('B' . $c, "=SUM(B5:B$c)")
            ->getStyle('B' . $c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $conveniosWorkSheet->setCellValue('C' . $c, "=SUM(C5:C$c)")
            ->getStyle('C' . $c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $conveniosWorkSheet->setCellValue('D' . $c, "=SUM(D5:C$c)")
            ->getStyle('D' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $conveniosWorkSheet->setCellValue('E' . $c, "=SUM(E5:C$c)")
            ->getStyle('E' . $c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $conveniosWorkSheet->setCellValue('F' . $c, "=SUM(F5:C$c)")
            ->getStyle('F' . $c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $conveniosWorkSheet->setCellValue('G' . $c, "=SUM(G5:C$c)")
            ->getStyle('G' . $c)->getNumberFormat()
            ->setFormatCode('#,##0');
        $conveniosWorkSheet->setCellValue('H' . $c, "=SUM(H5:C$c)")
            ->getStyle('H' . $c)->getNumberFormat()
            ->setFormatCode('#,##0');
    }


    /**
     * No conciliación
     * @param Worksheet $workSheet
     * @param $noconciliacion
     * @param $request
     */
    public function noConciliacion(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $workSheet, $noconciliacion, $request): void
    {
        $workSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $workSheet->getStyle('A3:D3')->applyFromArray($this->th1());
        $workSheet->getColumnDimension('B')->setAutoSize(true);
        $workSheet->getColumnDimension('C')->setAutoSize(true);
        $workSheet->getColumnDimension('D')->setAutoSize(true);

        $workSheet->getStyle('B3:D3')->getAlignment()->setWrapText(true);


        $workSheet->setCellValue('A1', 'NO CONCILIACIÓN');
        $workSheet->setCellValue('A3', 'CENTRO');
        $workSheet->setCellValue('B3', "No conciliación\n(procedimiento normal)");
        $workSheet->setCellValue('C3', "No conciliación\n(ratificación de\nconvenios -\nsolicitudes concluidas)");
        $workSheet->setCellValue('D3', "Total de solicitudes\ndonde se emitió\nconstancia de no\nconciliación");


        $c = 4;
        foreach ($this->imp as $centro) {
            $vlor =isset($noconciliacion[$centro]) ? $noconciliacion[$centro] : 0;
            $workSheet->setCellValue('A' . $c, $centro);
            //TODO: completar este dato
            $workSheet->setCellValue('B' . $c, 0);
            $workSheet->setCellValue('C' . $c,0);
            $workSheet->setCellValue('D' . $c,0);
            $c++;
        }
        $workSheet->setCellValue('A' . $c, 'Total');
        $workSheet->setCellValue('B' . $c, "=SUM(B3:B$c)")
            ->getStyle('B' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $workSheet->setCellValue('C' . $c, "=SUM(C3:C$c)")
            ->getStyle('C' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $workSheet->setCellValue('D' . $c, "=SUM(D3:D$c)")
            ->getStyle('D' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
    }

    /**
     * Las audiencias
     * @param Worksheet $workSheet
     * @param $audiencias
     * @param $request
     */
    public function audiencias(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $workSheet, $audiencias, $request): void
    {
        $workSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $workSheet->getStyle('A3:B3')->applyFromArray($this->th1());
        $workSheet->getColumnDimension('B')->setAutoSize(true);

        $workSheet->getStyle('B3')->getAlignment()->setWrapText(true);

        $workSheet->setCellValue('A1', 'AUDIENCIAS');
        $workSheet->setCellValue('A3', 'CENTRO');
        $workSheet->setCellValue('B3', "Audiencias\nconcluidas");

        $c = 4;
        foreach ($this->imp as $centro) {
            $vlor =isset($audiencias[$centro]) ? $audiencias[$centro] : 0;
            $workSheet->setCellValue('A' . $c, $centro);
            //TODO: completar este dato
            $workSheet->setCellValue('B' . $c, 0);
            $c++;
        }
        $workSheet->setCellValue('A' . $c, 'Total');
        $workSheet->setCellValue('B' . $c, "=SUM(B3:B$c)")
            ->getStyle('B' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
    }

    /**
     * @param Worksheet $workSheet
     * @param $pagosdiferidos
     * @param $request
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function pagosDiferidos(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $workSheet, $pagosdiferidos, $request): void
    {
        $workSheet->getStyle('A1')->applyFromArray($this->tituloH1());
        $workSheet->getStyle('A4:D4')->applyFromArray($this->th1());
        $workSheet->getColumnDimension('B')->setAutoSize(true);
        $workSheet->getColumnDimension('C')->setAutoSize(true);
        $workSheet->getColumnDimension('D')->setAutoSize(true);
        $workSheet->getStyle('B3:D3')->applyFromArray($this->boldcenter());

        $workSheet->mergeCells('B3:D3');
        $workSheet->setCellValue('B3', 'Pagos diferidos');

        $workSheet->getStyle('B3:D3')->getAlignment()->setWrapText(true);


        $workSheet->setCellValue('A1', 'PAGOS DIFERIDOS');
        $workSheet->setCellValue('A4', 'CENTRO');
        $workSheet->setCellValue('B4', "Vencidos");
        $workSheet->setCellValue('C4', "Incumplimiento");
        $workSheet->setCellValue('D4', "Pagado");


        $c = 5;
        foreach ($this->imp as $centro) {
            $vlor =isset($pagosdiferidos[$centro]) ? $pagosdiferidos[$centro] : 0;
            $workSheet->setCellValue('A' . $c, $centro);
            //TODO: completar este dato
            $workSheet->setCellValue('B' . $c, 0);
            $workSheet->setCellValue('C' . $c,0);
            $workSheet->setCellValue('D' . $c,0);
            $c++;
        }
        $workSheet->setCellValue('A' . $c, 'Total');
        $workSheet->setCellValue('B' . $c, "=SUM(B3:B$c)")
            ->getStyle('B' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $workSheet->setCellValue('C' . $c, "=SUM(C3:C$c)")
            ->getStyle('C' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $workSheet->setCellValue('D' . $c, "=SUM(D3:D$c)")
            ->getStyle('D' . $c)->getNumberFormat()
            ->setFormatCode('#,##0.00');
    }


    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Array a filas y columnas de excel
     * @TODO: Mejorar el algoritmo, talvez peuda eficientarse la velocidad de creación
     * @param $rows array Arreglo de los registros que se van a presentar en el excel
     * @param $sheet Worksheet Hoja en la que se va a vaciar el arreglo
     * @param $idx integer Fila desde la que se va a comenzar a vaciar el arreglo
     */
    private function arrayToExcel($rows, $sheet, $idx)
    {
        if (!$idx) {
            $idx = 1;
        }
        $id = 0;
        foreach ($rows as $row) {
            $c = 0;
            $vals = array_values($row);
            foreach ($this->excelColumnRange(count($row)) as $i => $value) {
                $sheet->setCellValue($value . $idx, $vals[$i]);
            }
            $idx++;
            $id++;
        }
    }

    /**
     * Regresa un arreglo con las columnas numeradas dada una cantidad de elementos, un número inicial de columna y una letra mínima de columna
     * @param $cant integer Número de columnas que se debe pasar a letras
     * @param $lower string Letra inicial con la que va a comenzar el conteo de columnas
     * @return \Generator
     */
    function excelColumnRange($cant, $lower = null)
    {
        if (!$lower) {
            $lower = 'A';
        }
        $col = 1;
        $band = true;
        while ($band) {
            if ($col >= $cant) {
                $band = false;
            }
            $col++;
            yield $lower;
            $lower++;
        }
    }

}
