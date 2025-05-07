<?php

namespace App\Repositories;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Admin;
use App\Models\User;
use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use App\Trait\LoggingError;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class AdminRepository implements BaseRepositoryInterface
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
            $query = Admin::select(['id', 'nama', 'no_hp', 'alamat', 'user_id']);
            if ($withRelations) {
                $query->with([
                    'user' => function ($query) {
                        $query->select(['id', 'email',]);
                    },
                    'user.roles' => function ($query) {
                        $query->select(['id', 'name']);
                    },
                ]);
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
            return Admin::with([
                'user' => function ($query) {
                    $query->select(['id', 'email']);
                },
                'user.roles' => function ($query) {
                    $query->select(['id', 'name']);
                },
            ])->find($id);
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
            return DB::transaction(function () use ($data) {
                $user = User::create([
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
                    'is_password_set' => $data['is_password_set'],
                ]);

                if (!$user) {
                    throw new DataAccessException('Failed to create User model within transaction.');
                }

                if (isset($data['role'])) {
                    $user->assignRole($data['role']);
                }

                $admin = Admin::create([
                    'nama' => $data['nama'],
                    'no_hp' => $data['no_hp'],
                    'alamat' => $data['alamat'],
                    'user_id' => $user->id,
                ]);

                if (!$admin) {
                    throw new DataAccessException('Failed to create Admin model within transaction.');
                }
                return $admin->load('user.roles');
            });
        } catch (QueryException $e) {
            $this->LogSqlException($e, $data);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['data' => $data]);
            throw new DataAccessException('Unexpected repository error during create transaction.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @param array $data
     * @return bool
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function update(string|int $id, array $data): bool|int
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $admin = Admin::find($id);
                if (!$admin) {
                    throw new ResourceNotFoundException("Admin with ID {$id} not found for update.");
                }

                if (isset($data['nama'])) {
                    $admin->nama = $data['nama'];
                }
                if (isset($data['no_hp'])) {
                    $admin->no_hp = $data['no_hp'];
                }
                if (isset($data['alamat'])) {
                    $admin->alamat = $data['alamat'];
                }

                if ($admin->user) {
                    if (isset($data['email'])) $admin->user->email = $data['email'];
                    if (isset($data['role'])) {
                        $admin->user->syncRoles([$data['role']]);
                    }
                    $userSaved = $admin->user->save();
                    if(!$userSaved) {
                        throw new DataAccessException("Failed to save related User data for Admin ID {$id}.");
                    }
                } else {
                    throw new DataAccessException("Related User not found for Admin ID {$id}.");
                }

                $adminSaved = $admin->save();
                if(!$adminSaved) {
                    throw new DataAccessException("Failed to save Admin data for ID {$id}.");
                }

                return true;

            }, 3);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id, 'data_baru' => $data]);
            throw $e;
        } catch (ResourceNotFoundException|DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data]);
            throw new DataAccessException('Unexpected repository error during update transaction.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function delete(string|int $id): bool|int
    {
        try {
            return DB::transaction(function () use ($id) {
                $admin = Admin::find($id);

                if (!$admin) {
                    throw new ResourceNotFoundException("Admin with ID {$id} not found for deletion.");
                }

                if ($admin->user) {
                    $userDeleted = $admin->user->delete();
                    if(!$userDeleted) {
                        throw new DataAccessException("Failed to delete related User for Admin ID {$id}.");
                    }
                } else {
                    $this->LogGeneralException(new \Exception("Related User not found for Admin ID {$id} during deletion."), ['id' => $id]);
                }

                $adminDeleted = $admin->delete();
                if (!$adminDeleted) {
                    throw new DataAccessException("Failed to delete Admin model for ID {$id}.");
                }


                return true;
            });
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (ResourceNotFoundException|DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Unexpected repository error during delete transaction.', 0, $e);
        }
    }
}
