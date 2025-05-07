<?php

namespace App\Exports;

use App\Models\PenyuluhTerdaftar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenyuluhTerdaftarExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return PenyuluhTerdaftar::with('kecamatan:id,nama')
            ->get()
            ->map(fn($item) => [
                'nama_penyuluh' => $item->nama,
                'no_hp' => $item->no_hp,
                'alamat' => $item->alamat,
                'kecamatan' => $item->kecamatan->nama,
            ]);
    }

    public function headings(): array
    {
        return ['Nama Penyuluh', 'No Hp', 'Alamat Lengkap', 'Kecamatan'];
    }

    public function styles(Worksheet $sheet): array
    {
        foreach (range('A', 'D') as $col) {
            $sheet->getStyle($col)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        }
        return [];
    }
}
