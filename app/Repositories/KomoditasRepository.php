<?php

namespace App\Repositories;

use App\Exceptions\DataAccessException;
use App\Models\Komoditas;
use App\Repositories\Interfaces\KomoditasRepositoryInterface;
use App\Trait\LoggingError;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Throwable;

class KomoditasRepository implements KomoditasRepositoryInterface
{
    use LoggingError;

    /**
     * @inheritDoc
     * @param $withRelations
     * @param array $criteria
     * @return Collection|array
     * @throws DataAccessException
     */
    public function getAll($withRelations = false, array $criteria = []): Collection|array
    {
        try {
            $query = Komoditas::select(['id', 'nama', 'deskripsi', 'musim']);

            $searchKomoditas = Arr::get($criteria, 'search_komoditas');
            $searchBibit = Arr::get($criteria, 'search_bibit');

            if ($searchKomoditas || $searchBibit) {
                $query->where(function ($query) use ($searchKomoditas, $searchBibit) {
                    if ($searchKomoditas) {
                        $query->where('nama', 'like', '%' . $searchKomoditas . '%');
                    }

                    if ($searchBibit) {
                        $query->orWhereHas('bibitBerkualitas', function ($relationQuery) use ($searchBibit) {
                            $relationQuery->where('nama', 'like', '%' . $searchBibit . '%');
                        });
                    }
                });
            }

            if ($withRelations) {
                $query->with(['bibitBerkualitas' => function ($relationQuery) use ($searchBibit) {
                    $relationQuery->select('id', 'komoditas_id', 'nama', 'deskripsi');
                    if ($searchBibit) {
                        $relationQuery->where('nama', 'like', '%' . $searchBibit . '%');
                    }
                }]);
            }

            return $query->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__, 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param $id
     * @return Model|null
     * @throws DataAccessException
     */
    public function getById($id): ?Model
    {
        try {
            return Komoditas::select(['id', 'nama', 'deskripsi', 'musim'])->where('id', $id)->first();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__, 0, $e);
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
            return Komoditas::create($data);
        } catch (QueryException $e) {
            $this->LogSqlException($e, $data);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['data' => $data]);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__, 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param $id
     * @param array $data
     * @return bool|int
     * @throws DataAccessException
     */
    public function update($id, array $data): bool|int
    {
        try {
            return Komoditas::where('id', $id)->update($data);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id, 'data_baru' => $data]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data]);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__, 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param $id
     * @return bool|int
     * @throws DataAccessException
     */
    public function delete($id): bool|int
    {
        try {
            return Komoditas::where('id', $id)->delete();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__, 0, $e);
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
            return Komoditas::count();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__, 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @return Collection
     * @throws DataAccessException
     */
    public function GetMusim(): Collection
    {
        try {
            return Komoditas::select(['nama', 'musim'])->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__, 0, $e);
        }
    }
}
