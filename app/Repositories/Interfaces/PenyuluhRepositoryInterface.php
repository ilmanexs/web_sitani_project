<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\Base\BaseRepositoryInterface;

interface PenyuluhRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Menghitung total Penyuluh yang sudah menggunakan aplikasi mobile sitani
     *
     * @return int Total Data Penyuluh
     */
    public function calculateTotal(): int;

    /**
     * Mengecek apakah penyuluh sudah memiliki akun Sitani(mobile)
     *
     * @param string|int $penyuluhTerdaftarId Penyuluh Terdaftar Id
     * @return bool Hasil
     */
    public function existsByPenyuluhTerdaftarId(string|int $penyuluhTerdaftarId): bool;
}
