<?php

namespace App\Services\Api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\KomoditasRepositoryInterface;
use App\Services\Interfaces\KomoditasApiServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Throwable;

class KomoditasApiService implements KomoditasApiServiceInterface
{
    protected KomoditasRepositoryInterface $repository;

    public function __construct(KomoditasRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param bool $withRelations
     * @param array $criteria
     * @inheritDoc
     * @return Collection
     * @throws DataAccessException
     */
    public function getAll(bool $withRelations = false, array $criteria = []): Collection
    {
        try {
            return $this->repository->getAll($withRelations, $criteria);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data komoditas.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat fetch data komoditas.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return Model
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function getById(string|int $id): Model
    {
        try {
            $komoditas = $this->repository->getById($id);

            if ($komoditas === null) {
                throw new ResourceNotFoundException("Komoditas dengan id {$id} tidak ditemukan.");
            }
            return $komoditas;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data komoditas dengan id {$id} .", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat fetch data komoditas dengan id {$id} .", 0, $e);
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
            return $this->repository->GetMusim();
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data musim.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat fetch data musim.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @return int
     * @throws DataAccessException
     */
    public function getTotal(): int
    {
        try {
            return $this->repository->calculateTotal();
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghitung total komoditas.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung total komoditas.', 0, $e);
        }
    }
}
