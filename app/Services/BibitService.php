<?php
namespace App\Services;

use App\Events\NotifGenerated;
use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ImportFailedException;
use App\Exports\BibitExport;
use App\Models\User;
use App\Repositories\Interfaces\BibitRepositoryInterface;
use App\Services\Interfaces\BibitServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BibitImport;

class BibitService implements BibitServiceInterface
{
    use LoggingError;

    protected BibitRepositoryInterface $repository;

    public function __construct(BibitRepositoryInterface $repository)
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
            throw new DataAccessException('Database error saat fetch data.', 500, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga.', 500, $e);
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
            $bibit = $this->repository->getById($id);

            if ($bibit === null) {
                throw new ResourceNotFoundException("Bibit dengan {$id} tidak ditemukan.");
            }

            return $bibit;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat mengambil data dengan id {$id}.", 500, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat mengambil data dengan id {$id}.", 500, $e);
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
            $bibit = $this->repository->create($data);
            if ($bibit === null) {
                throw new DataAccessException('Gagal menyimpan data bibit di repository.');
            }

            $users = User::role('penyuluh')->get();
            foreach ($users as $user) {
                event(new NotifGenerated(
                    $user,
                    'Bibit Baru Tersedia',
                    'Admin menambahkan bibit baru: ' . ($bibit->nama ?? 'bibit'),
                    'bibit_baru'
                ));
            }
            return $bibit;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menyimpan data bibit.', 500, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menyimpan data bibit.', 500, $e);
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
                throw new DataAccessException("Gagal memperbarui data bibit dengan id {$id} di repository.");
            }

            return (bool) $result;

        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat memperbarui data bibit dengan id {$id}.", 500, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat memperbarui data bibit dengan id {$id}.", 0, $e);
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
                throw new DataAccessException("Gagal menghapus data bibit dengan id {$id} di repository.");
            }
            return (bool) $result;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat menghapus data bibit dengan id {$id}.", 500, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat menghapus data bibit dengan id {$id}.", 500, $e);
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
            throw new DataAccessException('Database error saat menghitung total bibit.', 500, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung total bibit.', 500, $e);
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
            $import = new BibitImport();
            Excel::import($import, $file);

            return $import->getFailures();

        } catch (ValidationException $e) {
            $failures = $e->failures();
            throw new ImportFailedException("validasi import gagal.", 400, $e, $failures);

        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat import data bibit", 500, $e);
        } catch (Throwable $e) {
            throw new ImportFailedException("Terjadi kesalahan tidak terduga saat import data.", 500, $e);
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
            return new BibitExport();
        } catch (Throwable $e) {
            throw new DataAccessException("Gagal saat akan export data.", 500, $e);
        }
    }
}
