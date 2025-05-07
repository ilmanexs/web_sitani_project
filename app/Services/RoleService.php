<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Services\Interfaces\RoleServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Throwable;

class RoleService implements RoleServiceInterface
{
    use LoggingError;

    protected RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws DataAccessException
     */
    public function getAll($withRelations = false): Collection
    {
        try {
            return $this->repository->getAll($withRelations, []);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data role.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat fetch data role.', 0, $e);
        }
    }

    /**
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function getById(int|string $id): Model
    {
        try {
            $role = $this->repository->getById($id);

            if ($role === null) {
                throw new ResourceNotFoundException("Role dengan id {$id} tidak ditemukan.");
            }

            return $role;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data role dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tidak terduga saat fetch data role dengan id {$id}.", 0, $e);
        }
    }

    /**
     * @throws ResourceNotFoundException
     * @throws DataAccessException
     */
    public function createWithPermissions(array $roleData, array $permissionIds = []): Model
    {
        try {
            return DB::transaction(function () use ($roleData, $permissionIds) {
                $role = $this->repository->create($roleData);

                if ($role === null) {
                    throw new DataAccessException('Gagal menyimpan data role.');
                }

                $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

                $this->repository->syncPermissions($role->id, $permissionNames);

                $role->load('permissions');


                return $role;
            });
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menyimpan data role.', 0, $e);
        } catch (DataAccessException|ResourceNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menyimpan data role.', 0, $e);
        }
    }

    /**
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function updateRoleAndPermissions(int $id, array $roleData, array $permissionIds): Model
    {
        try {
            return DB::transaction(function () use ($id, $roleData, $permissionIds) {
                $updated = $this->repository->update($id, $roleData);

                if (!$updated) {
                    throw new DataAccessException("Gagal memperbarui data role dengan id {$id}.");
                }

                $role = $this->repository->getById($id);

                if ($role === null) {
                    throw new ResourceNotFoundException("Role dengan id {$id} tidak ditemukan untuk diperbarui.");
                }

                $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

                $this->repository->syncPermissions($role->id, $permissionNames);

                $role->load('permissions');

                return $role;
            });
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat memperbarui data role.', 0, $e);
        } catch (DataAccessException|ResourceNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan saat memperbarui data role.', 0, $e);
        }
    }

    /**
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function delete(int $id): bool
    {
        try {
            $deleted = $this->repository->delete($id);

            if (!$deleted) {
                throw new ResourceNotFoundException("Role dengan id {$id} tidak ditemukan untuk dihapus.");
            }
            return true;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghapus data role.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghapus data role.', 0, $e);
        }
    }
}
