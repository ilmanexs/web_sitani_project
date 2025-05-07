<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface PenyuluhTerdaftarRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Mengambil data penyuluh terdaftar berdasarkan Id kecamatan
     *
     * @param string|int $id
     * @return Collection
     */
    public function getByKecamatanId(string|int $id): Collection;

    /**
     * Mengambil data penyuluh terdaftar berdasarkan no hp
     *
     * @param string $phone Nomor hp Penyuluh
     * @return Model|null
     */
    public function getByPhone(string $phone): ?Model;

    /**
     * Menghitung total Penyuluh yang terdaftar di dinasi.
     * Belum pasti terdaftar/menggunakan aplikasi mobile.
     *
     * @return int Total Data Penyuluh
     */
    public function calculateTotal(): int;
}
