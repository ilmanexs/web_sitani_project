<?php

namespace App\Services\Api;

use App\Exceptions\DataAccessException;
use App\Repositories\Interfaces\BibitRepositoryInterface;
use App\Services\Interfaces\BibitApiServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Throwable;

class BibitApiService implements BibitApiServiceInterface
{
    protected BibitRepositoryInterface $repository;

    public function __construct(BibitRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @return Collection
     * @throws DataAccessException
     */
    public function getAllApi(): Collection
    {
        try {
            return $this->repository->getAll(false, []);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data bibit.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat fetch data bibit', 0, $e);
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
            throw new DataAccessException('Database error saat menghitung totol data bibit.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung total data bibit', 0, $e);
        }
    }
}
