<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ImportFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exports\KomoditasExport;
use App\Imports\KomoditasImport;
use App\Repositories\Interfaces\KomoditasRepositoryInterface;
use App\Services\Interfaces\KomoditasServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class KomoditasService implements KomoditasServiceInterface
{
    use LoggingError;

    protected KomoditasRepositoryInterface $repository;

    public function __construct(KomoditasRepositoryInterface $repository)
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
     * @param array $data
     * @return Model
     * @throws DataAccessException
     */
    public function create(array $data): Model
    {
        try {
            $komoditas = $this->repository->create($data);

            if ($komoditas === null) {
                throw new DataAccessException('Gagal menyimpan data komoditas di repository.');
            }

            return $komoditas;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menyimpan data komoditas.', 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menyimpan data komoditas.', 0, $e);
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
                throw new DataAccessException("Gagal memperbarui komoditas dengan id {$id} direpository.");
            }

            return (bool) $result;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat memperbarui komoditas dengan id {$id}.", 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat memperbarui data komoditas dengan id {$id}.", 0, $e);
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
                throw new DataAccessException("Gagal menghapus data komoditas dengan id {$id} di repository.");
            }

            return (bool) $result;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat menghapus data komoditas dengan id {$id}.", 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat menghapus data komoditas dengan id {$id}.", 0, $e);
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
            $import = new KomoditasImport();
            Excel::import($import, $file);

            return $import->getFailures();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            throw new ImportFailedException("Validasi data saat import Gagal.", 0, $e, collect($failures));
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat import data.", 0, $e);
        } catch (Throwable $e) {
            throw new ImportFailedException("Terjadi kesalahan tak terduga saat import data.", 0, $e);
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
            return new KomoditasExport();
        } catch (Throwable $e) {
            throw new DataAccessException("Gagal export data.", 0, $e);
        }
    }
}
