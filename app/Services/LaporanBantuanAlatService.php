<?php

namespace App\Services;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\PermintaanBantuanAlatRepositoryInterface;
use App\Services\Api\PermintaanBantuanAlatApiService;
use App\Services\Interfaces\LaporanBantuanAlatServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class LaporanBantuanAlatService implements LaporanBantuanAlatServiceInterface
{
    protected PermintaanBantuanAlatRepositoryInterface $repository;

    public function __construct(PermintaanBantuanAlatRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws DataAccessException
     */
    public function getAll(bool $withRelations = false): Collection
    {
        try {
            return $this->repository->getAll($withRelations);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data laporan bantuan alat.', 0, $e);
        } catch (\Throwable $th) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat fetch data laporan bantuan alat.', 0, $th);
        }
    }

    /**
     * @throws ResourceNotFoundException
     * @throws DataAccessException
     */
    public function getById(int|string $id): Model
    {
        try {
            $laporan = $this->repository->getById($id);
            if ($laporan === null) {
                throw new ResourceNotFoundException("Laporan bantuan alat dengan id " . $id . " tidak ditemukan.");
            }
            return $laporan;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data laporan bantuan alat dengan id " . $id . ".", 0, $e);
        } catch (\Throwable $th) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat fetch data laporan bantuan alat dengan id " . $id . ".", 0, $th);
        }
    }

    /**
     * Tidak digunakan, proses create resource berada di service khusus api
     *
     * @see PermintaanBantuanAlatApiService
     * @param array $data
     * @return Model|null
     */
    public function create(array $data): ?Model
    {
        return null;
    }

    /**
     * @throws DataAccessException
     */
    public function update(int|string $id, array $data): bool
    {
        try {
            $result = $this->repository->update($id, $data);
            if (!$result) {
                throw new DataAccessException("Gagal memperbarui laporan bantuan alat dengan id " . $id . ".");
            }
            return true;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat memperbarui data laporan bantuan alat.', 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat memperbarui data laporan bantuan alat.', 0, $th);
        }
    }

    /**
     * @throws DataAccessException
     */
    public function delete(int|string $id): bool
    {
        try {
            $result = $this->repository->delete($id);
            if (!$result) {
                throw new DataAccessException("Gagal menghapus laporan bantuan alat dengan id " . $id . ".");
            }
            return true;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghapus data laporan bantuan alat.', 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghapus data laporan bantuan alat.', 0, $th);
        }
    }
}
