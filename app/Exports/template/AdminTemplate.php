<?php

namespace App\Exports\template;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminTemplate implements FromArray, WithStyles
{

    public function array(): array
    {
        return [
            ['Nama Lengkap', 'No Hp', 'Email', 'Role', 'Alamat Lengkap'],
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
