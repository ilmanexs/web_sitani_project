<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\Base\BaseServiceInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

interface PenyuluhTerdaftarServiceInterface extends BaseServiceInterface
{
    /**
     * Mengambil data penyuluh terdaftar berdasarkan kecamatan id
     *
     * @param string|int $id
     * @return Collection
     */
    public function getByKecamatanId(string|int $id): Collection;

    /**
     * Mengambil total penyuluh terdaftar di dinas
     *
     * @return int total
     */
    public function calculateTotal(): int;

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
}
