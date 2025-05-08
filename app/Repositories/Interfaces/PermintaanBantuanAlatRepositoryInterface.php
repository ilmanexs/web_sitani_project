<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface PermintaanBantuanAlatRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * Mengambil seluruh laporan berdasarkan kecamatan id
     *
     * @param int|string $id kecamatan id
     * @return Collection Koleksi laporan permintaan bantuan alat
     */
    public function getAllByKecamatanId(int|string $id): Collection;

    /**
     * Mengambil total laporan permintaan bantuan alat berdasarkan kecamatan id
     *
     * @param int|string $id Kecamatan id
     * @return int Total
     */
    public function getTotalByKecamatanId(int|string $id): int;

    /**
     * Mengambil total laporan permintaan bantuan alat berdasarkan kecamatan id group by status(approved, rejected, dan pending)
     *
     * @param int|string|null $id Kecamatan id
     * @return array Total group by status
     */
    public function getStatsByKecamatanId(int|string|null $id): array;

    /**
     * Menyimpan detail laporan ke model laporan permintaan bantuan alat detail
     *
     * @param array $data data
     * @return Model|null Model laporan permintaan bantuan alat detail
     */
    public function insertLaporanDetail(array $data): ?Model;
}
