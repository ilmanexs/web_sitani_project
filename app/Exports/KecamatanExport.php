<?php

namespace App\Exports;

use App\Models\Kecamatan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KecamatanExport implements FromCollection, WithHeadings
{
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return Kecamatan::select('nama')->get();
    }

    public function headings(): array
    {
        return ['Nama'];
    }
}
