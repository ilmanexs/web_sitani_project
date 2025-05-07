<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\Base\BaseRepositoryInterface;

interface BibitRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Menghitung total Bibit Berkualitas yang terdaftar di aplikasi
     *
     * @return int Total Data Bibit
     */
    public function calculateTotal(): int;
}
