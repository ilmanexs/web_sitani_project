<?php

namespace App\Repositories;

use App\Exceptions\DataAccessException;
use App\Models\Kecamatan;
use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use App\Trait\LoggingError;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Throwable;

class KecamatanRepository implements BaseRepositoryInterface
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
            $query = Kecamatan::select(['id', 'nama']);
            if ($withRelations) {
                $query->with(['desa:id,nama']);
            }
            return $query->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' .  __METHOD__, 0, $e);
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
            return Kecamatan::where('id', $id)->first();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' .  __METHOD__, 0, $e);
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
            return Kecamatan::create($data);
        } catch (QueryException $e) {
            $this->LogSqlException($e, $data);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['data' => $data]);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' .  __METHOD__, 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @param array $data
     * @return Model|bool|int
     * @throws DataAccessException
     */
    public function update(string|int $id, array $data): bool|int
    {
        try {
            return Kecamatan::where('id', $id)->update($data);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id, 'data_baru' => $data]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data]);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' .  __METHOD__, 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return bool|int
     * @throws DataAccessException
     */
    public function delete(string|int $id): bool|int
    {
        try {
            return Kecamatan::destroy($id);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' .  __METHOD__, 0, $e);
        }
    }
}

