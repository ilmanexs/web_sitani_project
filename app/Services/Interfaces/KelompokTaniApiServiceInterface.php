<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface KelompokTaniApiServiceInterface
{
    /**
     * Mengambil seluruh data kelompok tani berdasarkan penyuluh id
     *
     * @param array $penyuluhIds penyuluh id
     * @return Collection Koleksi model Kelompok Tani
     */
    public function getAllByPenyuluhId(array $penyuluhIds): Collection;

    /**
     * Mengambil data kelompok tani berdasarkan kelompok tani id
     *
     * @param string|int $id kelompok tani id
     * @return Model Model kelompok tani
     */
    public function getById(string|int $id): Model;

    /**
     * Mengambil total kelompok tani yang terdaftar di Sitani
     *
     * @return int Total
     */
    public function getTotal(): int;

    /**
     * Mengambil total kelompok tani berdasarkan kecamatan id
     *
     * @param string|int $id Kecamatan id
     * @return int Total
     */
    public function countByKecamatanId(string|int $id): int;

    /**
     * Mengambil seluruh data kelompok tani berdasarkan kecamatan id
     *
     * @param string|int $kecamatanId Kecamatan id
     * @return Collection Koleksi Kelompok Tani
     */
    public function getAllByKecamatanId(string|int $kecamatanId, array $criteria = []): Collection;
}
