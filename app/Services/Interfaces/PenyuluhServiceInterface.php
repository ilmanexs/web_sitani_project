<?php

namespace App\Services\Interfaces;

use App\Exceptions\DataAccessException;
use App\Services\Interfaces\Base\BaseServiceInterface;

interface PenyuluhServiceInterface extends BaseServiceInterface
{
    /**
     * Mengambil total penyuluh yang terdaftar di Sitani
     *
     * @return int Total
     * @throws DataAccessException
     */
    public function calculateTotal(): int;

    /**
     * Mengecek apakah penyuluh sudah memiliki akun
     *
     * @param string|int $penyuluhTerdaftarId Penyuluh Terdaftar id
     * @return bool Hasil
     */
    public function existsByPenyuluhTerdaftarId(string|int $penyuluhTerdaftarId): bool;
}
