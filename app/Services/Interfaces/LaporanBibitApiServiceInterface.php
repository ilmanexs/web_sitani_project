<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface LaporanBibitApiServiceInterface
{
    /**
     * Menyimpan data laporan bibit
     *
     * @param array $data data laporan bibit
     * @return Model Model laporan bibit
     */
    public function create(array $data): Model;

    /**
     * Mengambil data laporan bibit berdasarkan penyuluh id
     *
     * @param string|int $penyuluhId penyuluh id
     * @return Collection Koleksi laporan bibit
     */
    public function getByKecamatanId(string|int $penyuluhId): Collection;

    /**
     * Mengambil data laporan statistik penggunaan bibit berdasarkan laporan yang tersimpan di Sitani
     *
     * @param string|int $kecamatanId Kecamatan id
     * @return array statistik laporan berdasarkan statusnya(berkualitas, tidak berkualitas, pending)
     */
    public function getLaporanStatusCounts(string|int $kecamatanId): array;

    /**
     * Mengambil total laporan bibit berdasarkan kecamatan id
     *
     * @param string|int $kecamatanId Kecamatan id
     * @return int Total
     */
    public function getTotalByKecamatanId(string|int $kecamatanId): int;

    /**
     * Mengambil total luas lahan secara keseluruhan dalam 5 tahun terakhir
     *
     * @return Collection Total luas lahan Group by Tahun
     */
    public function getTotalLuasLahan(): Collection;
}
