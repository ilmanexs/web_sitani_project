<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ImportFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\KelompokTaniRepositoryInterface;
use App\Repositories\Interfaces\ManyRelationshipManagement;
use App\Services\Interfaces\KelompokTaniServiceInterface;
use App\Trait\LoggingError;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KelompokTaniImport;
use App\Exports\KelompokTaniExport;
use Throwable;

class KelompokTaniService implements KelompokTaniServiceInterface
{
    use LoggingError;

    protected KelompokTaniRepositoryInterface $repository;
    protected ManyRelationshipManagement $relationManager;

    public function __construct(KelompokTaniRepositoryInterface $repository, ManyRelationshipManagement $relationManager)
    {
        $this->repository = $repository;
        $this->relationManager = $relationManager;
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
            throw new DataAccessException('Database error saat fetch data kelompok tani.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat fetch data kelompok tani.', 0, $e);
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
                throw new ResourceNotFoundException("Kelompok tani dengan id {$id} tidak ditemukan.");
            }
            return $kelompokTani;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch kelompok tani dengan id {$id}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tidak terduga saat fetch kelompok tani dengan id {$id}.", 0, $e);
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
            return DB::transaction(function () use ($data) {
                $kelompokTani = $this->repository->create(Arr::except($data, ['penyuluh_terdaftar_id']));

                if ($kelompokTani === null) {
                    throw new DataAccessException('Gagal menyimpan data kelompok tani.');
                }

                $penyuluhIds = $data['penyuluh_terdaftar_id'] ?? [];
                if (!empty($penyuluhIds)) {
                    $penyuluhIdsToAttach = Arr::flatten($penyuluhIds);
                    $this->relationManager->attach($kelompokTani, $penyuluhIdsToAttach);
                }

                $kelompokTani->load('penyuluhTerdaftars');

                return $kelompokTani;
            });
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menyimpan data kelompok tani.', 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menyimpan data kelompok tani.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param int|string $id
     * @param array $data
     * @return bool
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function update(int|string $id, array $data): bool
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $kelompokTani = $this->getById($id);
                $updated = $this->repository->update($id, Arr::except($data, ['penyuluh_terdaftar_id']));

                if (!$updated) {
                    throw new DataAccessException("Gagal memperbarui kelompok tani dengan id {$id}.");
                }

                $penyuluhIds = $data['penyuluh_terdaftar_id'] ?? [];
                $penyuluhIdsToSync = Arr::flatten($penyuluhIds);
                $this->relationManager->sync($kelompokTani, $penyuluhIdsToSync);


                return true;
            });
        } catch (ResourceNotFoundException|DataAccessException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat memperbarui data kelompok tani.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat memperbarui data kelompok tani.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param int|string $id
     * @return bool
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function delete(int|string $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $kelompokTani = $this->getById($id);

                $this->relationManager->detach($kelompokTani);
                $deleted = $this->repository->delete($id);

                if (!$deleted) {
                    throw new DataAccessException("Gagal menghapus data kelompok tani dengan id {$id}.");
                }

                return true;
            });
        } catch (ResourceNotFoundException|DataAccessException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghapus data kelompok tani.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghapus data kelompok tani', 0, $e);
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
            $import = new KelompokTaniImport();
            Excel::import($import, $file);

            $failures = $import->getFailures();

            if ($failures->isNotEmpty()) {
                throw new ImportFailedException("Import berhasil dengan beberapa kegagalan.", 0, null, $failures);
            }
            return [];
        } catch (ImportFailedException $e) {
            throw $e;
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw new DataAccessException("Database error saat import data.", 0, $e);
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new ImportFailedException("Terjadi kesalahan tidak terduga saat import data.", 0, $e);
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
            return new KelompokTaniExport();
        } catch (Throwable $e) {
            throw new DataAccessException("Gagal export data kelompok tani.", 0, $e);
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
            throw new DataAccessException('Database error saat menghitung total kelompok tani');
        } catch (DataAccessException $e) {
            throw new DataAccessException('Terjadi kesalahan saat menghitung total kelompok tani.');
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung total kelompok tani.');
        }
    }
}
