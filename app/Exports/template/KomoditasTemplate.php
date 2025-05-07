<?php

namespace App\Exports\template;

use Maatwebsite\Excel\Concerns\FromArray;

class KomoditasTemplate implements FromArray
{

    public function array(): array
    {
        return [
            ['Nama' , 'musim', 'deskripsi'],
        ];
    }
}
