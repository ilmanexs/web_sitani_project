<?php

namespace App\Repositories\Interfaces\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * Mengambil seluruh data
     *
     * @param bool $withRelations Default false, set true untuk mengambil data beserta relasi
     * @param array $criteria Set dengan nilai dari query parameter untuk menerapkan pencarian data berdasarkan query parameter
     * @return Collection|array Data
     */
    public function getAll(bool $withRelations = false, array $criteria = []): Collection|array;

    /**
     * Mengambil data berdasarkan id
     *
     * @param string|int $id Id data
     * @return ?Model
     */
    public function getById(string|int $id): ?Model;

    /**
     * Membuat data baru
     *
     * @param array $data Data baru
     * @return Model|null
     */
    public function create(array $data): ?Model;

    /**
     * Memperbarui data
     *
     * @param string|int $id Id data
     * @param array $data Data baru
     * @return bool
     */
    public function update(string|int $id, array $data): bool|int;

    /**
     * Menghapus data
     *
     * @param string|int $id Id data
     * @return bool
     */
    public function delete(string|int $id): bool|int;
}
