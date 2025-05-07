<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ImportFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use App\Services\Interfaces\KecamatanServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KecamatanImport;
use App\Exports\KecamatanExport;
use Throwable;

class KecamatanService implements KecamatanServiceInterface
{
    use LoggingError;

    protected BaseRepositoryInterface $repository;

    public function __construct(BaseRepositoryInterface $repository)
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
            throw new DataAccessException('Database error saat fetch data kecamatan.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat fetch kecamatan.', 0, $e);
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
            $kecamatan = $this->repository->getById($id);

            if ($kecamatan === null) {
                throw new ResourceNotFoundException("Kecamatan dengan id {$id} tidak ditemukan.");
            }
            return $kecamatan;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch kecamatan dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat fetch kecamatan dengan id {$id}.", 0, $e);
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
            $kecamatan = $this->repository->create($data);

            if ($kecamatan === null) {
                throw new DataAccessException('Gagal menyimpan data kecamatan di repository.');
            }

            return $kecamatan;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menyimpan data kecamatan.', 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menyimpan data kecamatan.', 0, $e);
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
                throw new DataAccessException("Gagal memperbarui data kecamatan dengan di {$id} di repository.");
            }

            return (bool) $result;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat memperbarui data kecamatan dengan id {$id}.", 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat memperbarui data kecamatan dengan id {$id}.", 0, $e);
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
                throw new DataAccessException("Gagal menghapus data kecamatan dengan id {$id} di repository.");
            }

            return (bool) $result;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat mengahapus data kecamatan dengan id {$id}.", 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat mengahapus data kecamatan dengan id {$id}.", 0, $e);
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
            $import = new KecamatanImport();
            Excel::import($import, $file);

            return $import->getFailures();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            throw new ImportFailedException("Gagal validasi data import.", 0, $e, collect($failures));
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat import data kecamatan.", 0, $e);
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
            return new KecamatanExport();
        } catch (Throwable $e) {
            throw new DataAccessException("Gagal export data.", 0, $e);
        }
    }
}
