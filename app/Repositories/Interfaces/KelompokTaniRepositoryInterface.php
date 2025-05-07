<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface KelompokTaniRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Mengambil data kelompok tani berdasarkan penyuluh id
     *
     * @param array $id Id penyuluh
     * @return Collection|array
     */
    public function getByPenyuluhId(array $id): Collection|array;

    /**
     * Menghitung total kelompok tani yang terdaftar di aplikasi
     *
     * @return int Total Data kelompok tani
     */
    public function calculateTotal(): int;

    /**
     * Mengambil total kelompok tani berdasarkan kecamatan id
     *
     * @param string|int $id kecamatan id
     * @return int
     */
    public function countByKecamatanId(string|int $id): int;

    /**
     * Mengambil seluruh data kelompok tani berdasarkan kecamatan id
     *
     * @param string|int $kecamatanId Kecamatan id
     * @param array $criteria Set dengan nilai dari query parameter untuk menerapkan pencarian data berdasarkan query parameter yang diambil
     * @return Collection
     */
    public function getAllByKecamatanId(string|int $kecamatanId, array $criteria = []): Collection;
}
