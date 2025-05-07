<?php

namespace App\Exports\template;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenyuluhTerdaftarTemplate implements FromArray, WithStyles
{

    public function array(): array
    {
        return [
            ['Nama Penyuluh', 'No Hp', 'Alamat Lengkap', 'Kecamatan']
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        foreach (range('A', 'E') as $col) {
            $sheet->getStyle($col)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        }
        return [];
    }
}
