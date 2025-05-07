<?php

namespace App\Repositories;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Trait\LoggingError;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role;
use Throwable;
use Illuminate\Database\QueryException;

class RoleRepository implements RoleRepositoryInterface
{
    use LoggingError;

    /**
     * @inheritDoc
     * @param bool $withRelations
     * @param array $criteria
     * @return Collection|array
     * @throws DataAccessException
     */
    public function getAll(bool $withRelations = false, array $criteria = []): Collection|array
    {
        try {
            $query = Role::query();
            if ($withRelations) {
                $query->with('permissions');
            }
            return $query->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Unexpected repository error in getAll.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return Model|null
     * @throws DataAccessException
     */
    public function getById(string|int $id): ?Model
    {
        try {
            return Role::with('permissions')->find($id);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Unexpected repository error in getById.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param array $data
     * @return Model|null
     * @throws DataAccessException
     */
    public function create(array $data): ?Model
    {
        try {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
            ]);

            if (!$role) {
                throw new DataAccessException('Failed to create Role model.');
            }

            return $role;
        } catch (QueryException $e) {
            $this->LogSqlException($e, $data);
            throw $e;
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['data' => $data]);
            throw new DataAccessException('Unexpected repository error during create.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @param array $data
     * @return bool|int
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function update(string|int $id, array $data): bool|int
    {
        try {
            $role = Role::findOrFail($id);
            $result = $role->update([
                'name' => $data['name'] ?? $role->name,
                'guard_name' => $data['guard_name'] ?? $role->guard_name,
            ]);

            if (!$result) {
                $this->LogGeneralException(new \Exception("Role update returned false."), ['id' => $id, 'data' => $data]);
            }

            return (bool)$result;
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id]);
            throw new ResourceNotFoundException("Role with ID {$id} not found for update.", 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id, 'data_baru' => $data]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data]);
            throw new DataAccessException('Unexpected repository error during update.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return bool|int
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function delete(string|int $id): bool|int
    {
        try {
            $role = Role::findOrFail($id);
            $result = (bool)$role->delete();

            if (!$result) {
                $this->LogGeneralException(new \Exception("Role delete returned false."), ['id' => $id]);
            }

            return $result;
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id]);
            throw new ResourceNotFoundException("Role with ID {$id} not found for deletion.", 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Unexpected repository error during delete.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param int $roleId
     * @param array $permissionNames
     * @return void
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function syncPermissions(int $roleId, array $permissionNames): void
    {
        try {

            $role = Role::findOrFail($roleId);

            $role->syncPermissions($permissionNames);

        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['role_id' => $roleId]);
            throw new ResourceNotFoundException("Role with ID {$roleId} not found for syncing permissions.", 0, $e);
        } catch (\InvalidArgumentException $e) {
            $this->LogGeneralException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw new DataAccessException("Invalid permission name provided for syncing.", 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw new DataAccessException('Unexpected repository error during syncPermissions.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param int $roleId
     * @param array $permissionNames
     * @return void
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function assignPermissions(int $roleId, array $permissionNames): void
    {
        try {
            $role = Role::findOrFail($roleId);
            $role->givePermissionTo($permissionNames);
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['role_id' => $roleId]);
            throw new ResourceNotFoundException("Role with ID {$roleId} not found for assigning permissions.", 0, $e);
        } catch (\InvalidArgumentException $e) {
            $this->LogGeneralException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw new DataAccessException("Invalid permission name provided for assigning.", 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw new DataAccessException('Unexpected repository error during assignPermissions.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param int $roleId
     * @param array $permissionNames
     * @return void
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function removePermissions(int $roleId, array $permissionNames): void
    {
        try {
            $role = Role::findOrFail($roleId);
            $role->revokePermissionTo($permissionNames);
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['role_id' => $roleId]);
            throw new ResourceNotFoundException("Role with ID {$roleId} not found for removing permissions.", 0, $e);
        } catch (\InvalidArgumentException $e) {
            $this->LogGeneralException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw new DataAccessException("Invalid permission name provided for removing.", 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['role_id' => $roleId, 'permission_names' => $permissionNames]);
            throw new DataAccessException('Unexpected repository error during removePermissions.', 0, $e);
        }
    }
}

