<?php

namespace App\Exports;

use App\Models\Komoditas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KomoditasExport implements FromCollection, WithHeadings
{
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return Komoditas::select(['nama', 'musim', 'deskripsi'])->get();
    }

    public function headings(): array
    {
        return ['Nama', 'Musim', 'Deskripsi'];
    }
}
