<?php

namespace App\Services\Api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\KelompokTaniRepositoryInterface;
use App\Services\Interfaces\KelompokTaniApiServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Throwable;

class KelompokTaniApiService implements KelompokTaniApiServiceInterface
{
    use LoggingError;

    protected KelompokTaniRepositoryInterface $repository;

    public function __construct(KelompokTaniRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @param array $penyuluhIds
     * @return Collection
     * @throws DataAccessException
     */
    public function getAllByPenyuluhId(array $penyuluhIds): Collection
    {
        try {
            return $this->repository->getByPenyuluhId($penyuluhIds);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data kelompok tani berdasarkan penyuluh id.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalaha tidak terduga saat fetch data kelompok tani berdasarkan penyuluh id.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param int|string $id
     * @return Model
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function getById(int|string $id): Model
    {
        try {
            $kelompokTani = $this->repository->getById($id);
            if ($kelompokTani === null) {
                throw new ResourceNotFoundException("Kelompok Tani dengan id {$id} tidak ditemukan.");
            }
            return $kelompokTani;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data kelompok tani dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tidak terduga saat fetch data Kelompok Tani dengan id {$id}.", 0, $e);
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
            throw new DataAccessException('Database error saat menghitung total data kelompok tani.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghitung total data kelompok tani.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param int|string $id
     * @return int
     * @throws DataAccessException
     */
    public function countByKecamatanId(int|string $id): int
    {
        try {
            return $this->repository->countByKecamatanId($id);
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat menghitung total data kelompok tani berdasarkan kecamatan dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tidak terduga saat menghitung total data kelompok tani berdasarkan kecamatan dengan id {$id}.", 0, $e);
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
            return $this->repository->getAllByKecamatanId($kecamatanId, $criteria);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data kelompok tani berdasarkan kecamatan id.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat fetch data kelompok tani berdasarkan kecamatan id.', 0, $e);
        }
    }
}
