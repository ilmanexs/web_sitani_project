<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ImportFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use App\Services\Interfaces\AdminServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Imports\AdminImport;
use App\Exports\AdminExport;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class AdminService implements AdminServiceInterface
{
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
            return $this->repository->getAll(true, []);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data admin.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat fetch data admin.', 0, $e);
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
            $admin = $this->repository->getById($id);

            if ($admin === null) {
                throw new ResourceNotFoundException("Admin dengan id {$id} tidak ditemukan.");
            }
            return $admin;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data admin dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tidak terduga saat fetch data admin dengan id {$id}.", 0, $e);
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
            $dt = [
                'email' => $data['email'],
                'nama' => $data['nama'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
                'password' => 'sitani',
                'role' => $data['role'],
                'is_password_set' => false,
            ];

            $admin = $this->repository->create($dt);

            if ($admin === null) {
                throw new DataAccessException('Gagal menyimpan data admin di repository.');
            }

            return $admin;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menyimpan data admin.', 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menyimpan data admin.', 0, $e);
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
            $updated = $this->repository->update($id, $data);

            if (!$updated) {
                throw new DataAccessException("Gagal memperbarui data admin dengan id {$id}.");
            }

            return true;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat memperbarui data admin dengan id {$id}.", 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tidak terduga saat memperbarui data admin dengan id {$id}.", 0, $e);
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
            $deleted = $this->repository->delete($id);

            if (!$deleted) {
                throw new DataAccessException("Gagal menghapus data admin dengan id {$id}.");
            }

            return true;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat menghapus data admin dengan id {$id}.", 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tidak terduga saat menghapus data admin dengan id {$id}.", 0, $e);
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
            $import = new AdminImport();
            Excel::import($import, $file);

            return $import->getFailures();
        } catch (ValidationException $e) {
            $failures = $e->failures();
            throw new ImportFailedException("Data gagal validasi saat import data admin.", 0, $e, collect($failures));
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat import data admin.", 0, $e);
        } catch (Throwable $e) {
            throw new ImportFailedException("Terjadi kesalahan tidak terduga saat import data admin.", 0, $e);
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
            return new AdminExport();
        } catch (Throwable $e) {
            throw new DataAccessException("Gagal export data admin.", 0, $e);
        }
    }
}
