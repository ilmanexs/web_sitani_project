<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\LaporanBibitRepositoryInterface;
use App\Services\Api\LaporanBibitApiService;
use App\Services\Interfaces\LaporanBibitServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class LaporanBibitService implements LaporanBibitServiceInterface
{
    use LoggingError;

    protected LaporanBibitRepositoryInterface $repository;

    public function __construct(LaporanBibitRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @param bool $withRelations
     * @return Collection
     * @throws DataAccessException
     */
    public function getAll(bool $withRelations = false): Collection
    {
        try {
            return $this->repository->getAll($withRelations, []);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data laporan bibit.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat fetch data laporan bibit.', 0, $e);
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
            $laporan = $this->repository->getById($id);

            if ($laporan === null) {
                throw new ResourceNotFoundException("Laporan Bibit dengan id {$id} tidak ditemukan.");
            }
            return $laporan;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data laporan bibit dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tidak terduga saat fetch data laporan bibit dengan id {$id}.", 0, $e);
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
    public function update(string|int $id, array $data): bool
    {
        try {
            $updated = $this->repository->update($id, $data);
            if (!$updated) {
                throw new ResourceNotFoundException("Laporan Bibit dengan id {$id} tidak ditemukan untuk diperbarui.");
            }
            return true;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat memperbarui data laporan bibit.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat memperbarui data laporan bibit.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return bool
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function delete(string|int $id): bool
    {
        try {
            $laporan = $this->getById($id);

            if ($laporan->laporanKondisiDetail && $laporan->laporanKondisiDetail->foto_bibit) {
                try {
                    Storage::disk('public')->delete($laporan->laporanKondisiDetail->foto_bibit);
                } catch (Throwable $fileDeleteError) {
                    $this->LogGeneralException($fileDeleteError, ['message' => 'Gagal menghapus file foto bibit terkait laporan', 'laporan_id' => $id]);
                }
            }

            $deleted = $this->repository->delete($id);

            if (!$deleted) {
                throw new DataAccessException("Gagal menghapus data laporan bibit dengan id {$id}.");
            }

            return true;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghapus data laporan bibit.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghapus data laporan bibit.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @note Tidak digunakan di service ini, pembuatan laporan berada pada laporan bibit api service
     * @see LaporanBibitApiService pembuatan laporan bibit ada disini.
     * @param array $data
     * @return Model|null
     */
    public function create(array $data): ?Model
    {
        return null;
    }

    /**
     * @inheritDoc
     * @return int
     * @throws DataAccessException
     */
    public function getTotal(): int
    {
        try {
            return $this->repository->getTotal();
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghitung total laporan bibit.');
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung total laporan bibit.');
        }
    }

    /**
     * @inheritDoc
     * @param int|string|null $penyuluhId
     * @return array
     * @throws DataAccessException
     */
    public function getLaporanStatusCounts(int|string|null $penyuluhId): array
    {
        try {
            return $this->repository->getLaporanStatusCounts($penyuluhId);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghitung total laporan bibit berdasarkan status.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghitung total laporan bibit berdasarkan status.', 0, $e);
        }
    }
}
