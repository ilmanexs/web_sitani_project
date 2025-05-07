<?php

namespace App\Exports;

use App\Models\Desa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DesaExport implements FromCollection, WithHeadings
{
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return Desa::with('kecamatan:id,nama')
            ->get()
            ->map(function ($desa) {
                return [
                    'nama_desa' => $desa->nama,
                    'nama_kecamatan' => $desa->kecamatan->nama ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return ['Nama Desa', 'Nama Kecamatan'];
    }
}
