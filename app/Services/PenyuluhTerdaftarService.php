<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ImportFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Imports\PenyuluhTerdaftarImport;
use App\Repositories\Interfaces\PenyuluhTerdaftarRepositoryInterface;
use App\Services\Interfaces\PenyuluhTerdaftarServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\PenyuluhTerdaftarExport;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class PenyuluhTerdaftarService implements PenyuluhTerdaftarServiceInterface
{
    use LoggingError;

    protected PenyuluhTerdaftarRepositoryInterface $repository;

    public function __construct(PenyuluhTerdaftarRepositoryInterface $repository)
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
            throw new DataAccessException('Database error saat fetch data penyuluh terdaftar.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat saat fetch data penyuluh terdaftar.', 0, $e);
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

            if ($penyuluh === null) {
                throw new ResourceNotFoundException("Penyuluh terdaftar dengan id {$id} tidak ditemukan.");
            }

            return $penyuluh;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data penyuluh terdaftar dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat fetch data penyuluh terdaftar dengan id {$id}.", 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param array $data
     * @return Model
     * @throws DataAccessException
     */
    public function create(array $data): Model
    {
        try {
            $penyuluh = $this->repository->create($data);

            if ($penyuluh === null) {
                throw new DataAccessException('Gagal menyimpan data penyuluh terdaftar di repository.');
            }

            return $penyuluh;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menyimpan data penyuluh terdaftar.', 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menyimpan data penyuluh terdaftar.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @param array $data
     * @return bool
     * @throws DataAccessException
     */
    public function update(string|int $id, array $data): bool
    {
        try {
            $result = $this->repository->update($id, $data);

            if(!$result) {
                throw new DataAccessException("Gagal memperbarui data penyuluh terdaftar dengan id {$id} di repository.");
            }
            return (bool) $result;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat memperbarui data penyuluh terdaftar dengan id {$id}.", 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat memperbarui data penyuluh terdaftar dengan id {$id}.", 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return bool
     * @throws DataAccessException
     */
    public function delete(string|int $id): bool
    {
        try {
            $result = $this->repository->delete($id);

            if (!$result) {
                throw new DataAccessException("Gagal menghapus data penyuluh terdaftar dengan id {$id} di repository.");
            }

            return (bool) $result;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat menghapus data penyuluh terdaftar dengan id {$id}.", 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat menghapus data penyuluh terdaftar dengan id {$id}.", 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return Collection
     * @throws DataAccessException
     */
    public function getByKecamatanId(string|int $id): Collection
    {
        try {
            return $this->repository->getByKecamatanId($id);
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data penyuluh terdaftar berdasarkan kecamatan dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat fetch data penyuluh terdaftar berdasarkan kecamatan dengan id {$id}.", 0, $e);
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
            return $this->repository->calculateTotal();
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghitung total penyuluh terdaftar.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung total penyuluh terdaftar.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param mixed $file
     * @return array
     * @throws DataAccessException
     * @throws ImportFailedException
     */
    public function import(mixed $file): array
    {
        try {
            $import = new PenyuluhTerdaftarImport();
            Excel::import($import, $file);

            return $import->getFailures();
        } catch (ValidationException $e) {
            $failures = $e->failures();
            throw new ImportFailedException("Validasi gagal saat import data.", 0, $e, collect($failures));
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat import data penyuluh terdaftar.", 0, $e);
        } catch (Throwable $e) {
            throw new ImportFailedException("Terjadi kesalahan tak terduga saat import data penyuluh terdaftar.", 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @return FromCollection
     * @throws DataAccessException
     */
    public function export(): FromCollection
    {
        try {
            return new PenyuluhTerdaftarExport();
        } catch (Throwable $e) {
            throw new DataAccessException("Gagal export data penyuluh terdaftar.", 0, $e);
        }
    }
}
