<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\Base\BaseServiceInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

interface DesaServiceInterface extends BaseServiceInterface
{
    /**
     * Mengambil data berdasarkan kecamatan id
     *
     * @param string|int $id kecamatan id
     * @return Collection
     */
    public function getByKecamatanId(string|int $id): Collection;

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
