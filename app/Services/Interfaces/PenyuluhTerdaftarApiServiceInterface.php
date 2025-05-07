<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface PenyuluhTerdaftarApiServiceInterface
{
    /**
     * Mengambil data penyuluh terdaftar berdasarkan no hp
     *
     * @param string $phone No Hp penyuluh
     * @return Model Model penyuluh terdaftar
     */
    public function getByPhone(string $phone): Model;
}
