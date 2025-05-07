<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface KomoditasRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Menghitung total Komoditas yang terdaftar di aplikasi
     *
     * @return int Total Data Komoditas
     */
    public function calculateTotal(): int;

    /**
     * Mengambil data musim tiap komoditas
     *
     * @return Collection data
     */
    public function GetMusim(): Collection;
}
