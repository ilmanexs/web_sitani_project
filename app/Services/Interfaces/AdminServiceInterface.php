<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\Base\BaseServiceInterface;
use Maatwebsite\Excel\Concerns\FromCollection;

interface AdminServiceInterface extends BaseServiceInterface
{
    /**
     * Import data menggunakan excel
     *
     * @param mixed $file File Excel
     * @return array
     */
    public function import(mixed $file): array;

    /**
     * Export data dalam bentuk excel
     *
     * @return FromCollection
     */
    public function export(): FromCollection;
}
