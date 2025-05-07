<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\Base\BaseServiceInterface;
use Maatwebsite\Excel\Concerns\FromCollection;

interface KelompokTaniServiceInterface extends BaseServiceInterface
{
    /**
     * Import data menggunakan file excel
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

    /**
     * Mengambil total kelompok tani yang terdaftar di Sitani
     *
     * @return int total
     */
    public function getTotal(): int;
}
