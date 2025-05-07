<?php

namespace App\Services\Interfaces\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseServiceInterface
{
    /**
     * Mengambil seluruh resource beserta relasinya.
     *
     * @param bool $withRelations set true untuk get resource beserta relasinya.
     * @return Collection Koleksi resource.
     */
    public function getAll(bool $withRelations = false): Collection;

    /**
     * Mengambil resource berdasarkan id resource
     *
     * @param string|int $id id resource
     * @return Model Model resource
     */
    public function getById(string|int $id): Model;

    /**
     * Membuat resource baru
     *
     * @param array $data Data
     * @return Model|null Model resource
     */
    public function create(array $data): ?Model;

    /**
     * Memperbarui resource
     *
     * @param string|int $id id resource
     * @param array $data data baru
     * @return bool hasil
     */
    public function update(string|int $id, array $data): bool;

    /**
     * Menghapus resource
     *
     * @param string|int $id id resource
     * @return bool hasil
     */
    public function delete(string|int $id): bool;
}
