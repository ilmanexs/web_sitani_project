<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RoleServiceInterface
{
    public function getAll($withRelations = false): Collection;

    public function getById(int|string $id): Model;

    public function createWithPermissions(array $roleData, array $permissionIds = []): Model;

    public function updateRoleAndPermissions(int $id, array $roleData, array $permissionIds): Model;

    public function delete(int $id): bool;
}
