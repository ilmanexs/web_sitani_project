<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\Base\BaseServiceInterface;
use Maatwebsite\Excel\Concerns\FromCollection;

interface BibitServiceInterface extends BaseServiceInterface
{
    /**
     * Mengambil total data bibit berkualitas yang terdaftar di Sitani
     *
     * @return int total
     */
    public function getTotal(): int;

    /**
     * Import data menggunakan excel
     *
     * @param mixed $file file excel
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
