<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface PermintaanAlatApiServiceInterface
{
    /**
     * Mengambil seluruh data permintaan bantuan alat berdasarkan kecamatan id
     *
     * @param string|int $id Kecamatan id
     * @return Collection Koleksi permintaan bantuan alat
     */
    public function getAllByKecamatanId(string|int $id): Collection;

    /**
     * Menyimpan laporan permintaan bantuan alat oleh penyuluh
     *
     * @param array $data Data
     * @return Model|null Model permintaan alat
     */
    public function create(array $data): ?Model;

    /**
     * Mengambil total laporan permintaan bantuan alat berdasarkan kecamatan id(keseluruhan)
     *
     * @param string|int $id Kecamatan id
     * @return int Total
     */
    public function getTotalByKecamatanId(string|int $id): int;

    /**
     * Mengambil total laporan permintaan bantuan alat berdasarkan kecamatan id, group by status(approved, rejected, pending)
     *
     * @param string|int $id Kecamatan id
     * @return array Total laporan group by status
     */
    public function getStatsByKecamatanId(string|int $id): array;
}
