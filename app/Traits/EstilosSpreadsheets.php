<?php

namespace App\Traits;

trait EstilosSpreadsheets {


    public function tituloH1()
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'name' => 'Arial',
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ];

        return $styleArray;
    }

    public function th1()
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'name' => 'Arial',
                'size' => 11
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFDAE3F3',
                ],
            ],
        ];

        return $styleArray;
    }
    public function boldcenter()
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'name' => 'Arial',
                'size' => 11
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];

        return $styleArray;
    }
    public function tf1()
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'name' => 'Arial',
                'size' => 11
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFDAE3F3',
                ],
            ],
        ];

        return $styleArray;
    }

    public function tbody()
    {
        $styleArray = [
            'font' => [
                'name' => 'Arial',
                'size' => 10
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        return $styleArray;
    }

}
