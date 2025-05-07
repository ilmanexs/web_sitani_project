<?php

namespace App\Exports\template;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelompokTaniTemplate implements FromArray, WithStyles
{
    /**
     * Mengatur struktur template
     *
     * @return array[] struktur template
     */
    public function array(): array
    {
        return [
            ['Nama Kelompok Tani', 'Desa', 'Kecamatan', 'No Hp Penyuluh'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        foreach (range('A', 'D') as $col) {
            $sheet->getStyle($col)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        }
        return [];
    }
}
