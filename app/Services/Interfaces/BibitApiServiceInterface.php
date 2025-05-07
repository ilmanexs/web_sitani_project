<?php

namespace App\Services\Interfaces;

use Illuminate\Support\Collection;

interface BibitApiServiceInterface
{
    /**
     * Mengambil seluruh data bibit berkualitas yang terdaftar di Sitani
     *
     * @return Collection Koleksi Data Bibit
     */
    public function getAllApi(): Collection;

    /**
     * Mengambil total bibit berkualitas yang terdaftar di Sitani
     *
     * @return int total
     */
    public function getTotal(): int;
}
