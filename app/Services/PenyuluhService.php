<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\PenyuluhRepositoryInterface;
use App\Services\Interfaces\PenyuluhServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Throwable;

class PenyuluhService implements PenyuluhServiceInterface
{
    use LoggingError;

    protected PenyuluhRepositoryInterface $repository;

    public function __construct(PenyuluhRepositoryInterface $repository)
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
            return $this->repository->getAll(true, []);
        } catch (QueryException $e) {
            $this->LogSqlException($e, [], 'Database error saat mengambil semua data penyuluh.');
            throw new DataAccessException('Gagal mengambil data penyuluh.', 0, $e);
        } catch (Throwable $e) {
            $this->LogGeneralException($e, [], 'Terjadi kesalahan tak terduga saat mengambil semua data penyuluh.');
            throw new DataAccessException('Terjadi kesalahan tak terduga saat mengambil data penyuluh.', 0, $e);
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
            $penyuluh = $this->repository->getById($id);

            if (!$penyuluh) {
                throw new ResourceNotFoundException('Data penyuluh dengan id ' . $id . ' tidak ditemukan.');
            }

            return $penyuluh;

        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id], 'Database error saat mencari data penyuluh berdasarkan ID.');
            throw new DataAccessException('Gagal mengambil data penyuluh.', 0, $e);
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id], 'Terjadi kesalahan tak terduga saat mencari data penyuluh berdasarkan ID.');
            throw new DataAccessException('Terjadi kesalahan tak terduga saat mengambil data penyuluh.', 0, $e);
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
            $penyuluh = $this->repository->create($data);

            if (!$penyuluh) {
                $this->LogGeneralException(new \Exception('Repository create returned null.'), $data, 'Repository gagal membuat penyuluh.');
                throw new DataAccessException('Gagal membuat data penyuluh.');
            }

            return $penyuluh;

        } catch (QueryException $e) {
            $this->LogSqlException($e, $data, 'Database error saat membuat data penyuluh.');
            throw new DataAccessException('Terjadi kesalahan saat registrasi.', 0, $e);
        } catch (Throwable $e) {
            $this->LogGeneralException($e, $data, 'Terjadi kesalahan tak terduga saat membuat data penyuluh.');
            throw new DataAccessException('Terjadi kesalahan tak terduga saat registrasi.', 0, $e);
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
            return $this->repository->update($id, $data);

        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id], 'Penyuluh tidak ditemukan untuk diupdate.');
            throw new ResourceNotFoundException('Data penyuluh dengan id ' . $id . ' tidak ditemukan.', 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id, 'data_baru' => $data], 'Database error saat memperbarui data penyuluh.');
            throw new DataAccessException('Terjadi kesalahan saat mengupdate data penyuluh.', 0, $e);
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data], 'Terjadi kesalahan tak terduga saat memperbarui data penyuluh.');
            throw new DataAccessException('Terjadi kesalahan tak terduga saat mengupdate data penyuluh.', 0, $e);
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
            return $this->repository->delete($id);

        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id], 'Penyuluh tidak ditemukan untuk dihapus.');
            throw new ResourceNotFoundException('Data penyuluh dengan id ' . $id . ' tidak ditemukan.', 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id], 'Database error saat menghapus data penyuluh.');
            throw new DataAccessException('Terjadi kesalahan saat menghapus data penyuluh.', 0, $e);
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id], 'Terjadi kesalahan tak terduga saat menghapus data penyuluh.');
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghapus data penyuluh.', 0, $e);
        }
    }

    public function calculateTotal(): int
    {
        try {
            return $this->repository->calculateTotal();
        } catch (QueryException $e) {
            $this->LogSqlException($e, [], 'Database error saat menghitung total data penyuluh.');
            throw new DataAccessException('Gagal menghitung total penyuluh.', 0, $e);
        } catch (Throwable $e) {
            $this->LogGeneralException($e, [], 'Terjadi kesalahan tak terduga saat menghitung total data penyuluh.');
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung total penyuluh.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function existsByPenyuluhTerdaftarId(int|string $penyuluhTerdaftarId): bool
    {
        try {
            return $this->repository->existsByPenyuluhTerdaftarId($penyuluhTerdaftarId);
        } catch (QueryException $e) {
            throw new DataAccessException('Gagal mengecek akun penyuluh.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat mengecek akun penyuluh.', 0, $e);
        }
    }
}
