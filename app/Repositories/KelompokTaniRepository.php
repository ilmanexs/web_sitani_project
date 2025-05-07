<?php

namespace App\Repositories;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\KelompokTani;
use App\Repositories\Interfaces\KelompokTaniRepositoryInterface;
use App\Repositories\Interfaces\ManyRelationshipManagement;
use App\Trait\LoggingError;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Throwable;

class KelompokTaniRepository implements ManyRelationshipManagement, KelompokTaniRepositoryInterface
{
    use LoggingError;

    /**
     * @param array $criteria
     * @inheritDoc
     * @return Collection|array
     * @throws DataAccessException
     */
    public function getAll(bool $withRelations = false, array $criteria = []): Collection|array
    {
        try {
            $query = KelompokTani::query();
            if ($withRelations) {
                $query->with([
                    'kecamatan' => fn ($q) => $q->select('id', 'nama'),
                    'desa' => fn ($q) => $q->select('id', 'nama'),
                    'penyuluhTerdaftars' => fn ($q) => $q->select('penyuluh_terdaftars.id', 'nama')
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
     * @return ?Model
     * @throws DataAccessException
     */
    public function getById(int|string $id): ?Model
    {
        try {
            return KelompokTani::where('id', $id)->with([
                'penyuluhTerdaftars' => fn ($q) => $q->select('penyuluh_terdaftars.id', 'nama', 'no_hp', 'alamat'),
                'kecamatan' => fn ($q) => $q->select('id', 'nama'),
                'desa' => fn ($q) => $q->select('id', 'nama')
            ])->first();
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
     * @return ?Model
     * @throws DataAccessException
     */
    public function create(array $data): ?Model
    {
        try {
            $model = KelompokTani::create($data);
            if (!$model) {
                throw new DataAccessException('Failed to create KelompokTani model.');
            }
            return $model;
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
     * @return bool|int
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function update(int|string $id, array $data): bool|int
    {
        try {
            $model = KelompokTani::findOrFail($id);
            $result = $model->update($data);

            if(!$result) {
                $this->LogGeneralException(new \Exception("KelompokTani update returned false."), ['id' => $id, 'data' => $data]);
            }

            return (bool) $result;
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id]);
            throw new ResourceNotFoundException("KelompokTani with ID {$id} not found for update.", 0, $e);
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
     * @return bool|int
     * @throws DataAccessException|ResourceNotFoundException
     */
    public function delete(int|string $id): bool|int
    {
        try {
            $model = KelompokTani::findOrFail($id);
            $result = $model->delete();

            if (!$result) {
                $this->LogGeneralException(new \Exception("KelompokTani delete returned false."), ['id' => $id]);
            }

            return (bool) $result;
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id]);
            throw new ResourceNotFoundException("KelompokTani with ID {$id} not found for deletion.", 0, $e);
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
     * @return Collection|array
     * @throws DataAccessException
     */
    public function getByPenyuluhId(array $id): Collection|array
    {
        try {
            return KelompokTani::whereHas('penyuluhTerdaftars', function ($query) use ($id) {
                $query->whereIn('penyuluh_terdaftars.id', $id);
            })
                ->with([
                    'desa:id,nama,kecamatan_id',
                    'kecamatan:id,nama',
                    'penyuluhTerdaftars:id,nama,no_hp,alamat',
                ])
                ->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['penyuluh_ids' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['penyuluh_ids' => $id]);
            throw new DataAccessException('Unexpected repository error in getByPenyuluhId.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @return int
     * @throws DataAccessException
     */
    public function calculateTotal(): int
    {
        try {
            return KelompokTani::count();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Unexpected repository error in calculateTotal.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @return int
     * @throws DataAccessException
     */
    public function countByKecamatanId(int|string $id): int
    {
        try {
            return KelompokTani::where('kecamatan_id', $id)->count();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['kecamatan_id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['kecamatan_id' => $id]);
            throw new DataAccessException('Unexpected repository error in countByKecamatanId.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @return bool
     * @throws DataAccessException
     */
    public function attach(Model $model, array|int|Collection $ids, array $attributes = []): bool
    {
        try {
            if ($model instanceof KelompokTani) {
                $model->penyuluhTerdaftars()->attach($ids, $attributes);
                return true;
            }
            throw new DataAccessException('Invalid model provided for attach.');
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['model_id' => $model->id ?? 'N/A', 'ids' => $ids]);
            throw $e;
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['model_id' => $model->id ?? 'N/A', 'ids' => $ids]);
            throw new DataAccessException('Unexpected repository error during attach.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @return bool
     * @throws DataAccessException
     */
    public function detach(Model $model, int|array|Collection|null $ids = null): ?int
    {
        try {
            if ($model instanceof KelompokTani) {
                return $model->penyuluhTerdaftars()->detach($ids);
            }
            throw new DataAccessException('Invalid model provided for detach.');
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['model_id' => $model->id ?? 'N/A', 'ids' => $ids]);
            throw $e;
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['model_id' => $model->id ?? 'N/A', 'ids' => $ids]);
            throw new DataAccessException('Unexpected repository error during detach.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @return ?array
     * @throws DataAccessException
     */
    public function sync(Model $model, array $relations, bool $detaching = true): ?array
    {
        try {
            if ($model instanceof KelompokTani) {
                return $model->penyuluhTerdaftars()->sync($relations, $detaching);
            }
            throw new DataAccessException('Invalid model provided for sync.');
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['model_id' => $model->id ?? 'N/A', 'relations' => $relations]);
            throw $e;
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['model_id' => $model->id ?? 'N/A', 'relations' => $relations]);
            throw new DataAccessException('Unexpected repository error during sync.', 0, $e);
        }
    }

    /**
     * @param int|string $kecamatanId
     * @param array $criteria
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getAllByKecamatanId(int|string $kecamatanId, array $criteria = []): Collection
    {
        try {
            $query = KelompokTani::query();

            $query->where('kecamatan_id', $kecamatanId);

            $searchNamaKelompokTani = Arr::get($criteria, 'search_nama_kelompok_tani');
            $searchNamaDesa = Arr::get($criteria, 'search_nama_desa');

            if ($searchNamaKelompokTani || $searchNamaDesa) {
                $query->where(function ($query) use ($searchNamaKelompokTani, $searchNamaDesa) {
                    $firstConditionAdded = false;

                    if ($searchNamaKelompokTani) {
                        $query->where('nama', 'like', '%' . $searchNamaKelompokTani . '%');
                        $firstConditionAdded = true;
                    }

                    if ($searchNamaDesa) {
                        if ($firstConditionAdded) {
                            $query->orWhereHas('desa', function ($relationQuery) use ($searchNamaDesa) {
                                $relationQuery->where('nama', 'like', '%' . $searchNamaDesa . '%');
                            });
                        } else {
                            $query->whereHas('desa', function ($relationQuery) use ($searchNamaDesa) {
                                $relationQuery->where('nama', 'like', '%' . $searchNamaDesa . '%');
                            });
                        }
                    }
                });
            }

            $query->with(['kecamatan:id,nama', 'desa:id,nama']);

            return $query->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__, 0, $e);
        }
    }
}
