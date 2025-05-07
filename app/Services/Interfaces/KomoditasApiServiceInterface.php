<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface KomoditasApiServiceInterface
{
    /**
     * Mengambil seluruh data Komoditas yang terdaftar di Sitani
     *
     * @param bool $withRelations set true untuk mengambil data beserta relasi, default false
     * @return Collection Koleksi model komoditas
     */
    public function getAll(bool $withRelations = false, array $criteria = []): Collection;

    /**
     * Mengambil data komoditas berdasarkan komoditas id
     *
     * @param string|int $id id komoditas
     * @return Model Model Komoditas
     */
    public function getById(string|int $id): Model;

    /**
     * Mengambil jumlah musim tiap komoditas
     *
     * @return Collection Koleksi komoditas beserta jumlah musimnya
     */
    public function GetMusim(): Collection;

    /**
     * Mengambil total komoditas yang terdaftar di Sitani
     *
     * @return int Total
     */
    public function getTotal(): int;
}
