<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\Base\BaseServiceInterface;

interface LaporanBibitServiceInterface extends BaseServiceInterface
{
    /**
     * Mengambil total kelompok tani yang terdaftar di Sitani
     *
     * @return int total
     */
    public function getTotal(): int;

    /**
     * Mengambil total status laporan tiap kategori(berkualitas, tidak berkualitas, dan pending/menunggu konfirmasi)
     *
     * @param string|int|null $penyuluhId penyuluh id
     * @return array data total laporan
     */
    public function getLaporanStatusCounts(string|int|null $penyuluhId): array;
}
