<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface DesaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Mengambil data desa berdasarkan Id kecamatan
     *
     * @param int|string $id Id kecamatan
     * @return Collection
     */
    public function getByKecamatanId(int|string $id): Collection;
}
