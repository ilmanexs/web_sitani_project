<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\Base\BaseRepositoryInterface;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Menyinkronkan permission dengan role
     *
     * @param int $roleId
     * @param array $permissionNames
     * @return void
     */
    public function syncPermissions(int $roleId, array $permissionNames): void;

    /**
     * Menambahkan permission ke role
     *
     * @param int $roleId
     * @param array $permissionNames
     * @return void
     */
    public function assignPermissions(int $roleId, array $permissionNames): void;

    /**
     * Menghapus permission dari role
     *
     * @param int $roleId
     * @param array $permissionNames
     * @return void
     */
    public function removePermissions(int $roleId, array $permissionNames): void;
}
