<?php

namespace App\Services;

use App\Events\NotifGenerated;
use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Exports\LaporanBantuanAlatExport;
use App\Models\User;
use App\Repositories\Interfaces\PermintaanBantuanAlatRepositoryInterface;
use App\Services\Api\PermintaanBantuanAlatApiService;
use App\Services\Interfaces\LaporanBantuanAlatServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

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
            DB::beginTransaction();

            $laporanBantuanAlat = $this->repository->getById($id);

            if (!$laporanBantuanAlat) {
                throw new DataAccessException("Gagal mengambil laporan bantuan alat dengan id " . $id . ".");
            }

            $result = $this->repository->update($id, $data);
            if (!$result) {
                throw new DataAccessException("Gagal memperbarui laporan bantuan alat dengan id " . $id . ".");
            }

            $this->handleStatusChange($laporanBantuanAlat, $data['status'] ?? null);

            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new DataAccessException('Database error saat memperbarui data laporan bantuan alat.', 0, $e);
        } catch (DataAccessException $e) {
            DB::rollBack();
            throw $e;
        } catch (Throwable $th) {
            DB::rollBack();
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

    private function handleStatusChange(Model $laporanBantuanAlat, ?int $status): void
    {
        if ($status === null) {
            return;
        }

        $penyuluh = $laporanBantuanAlat->penyuluh()->first();
        if (!$penyuluh) {
            return;
        }

        $user = User::find($penyuluh->user_id);
        if (!$user) {
            return;
        }

        $judulNotifikasi = '';
        $pesanNotifikasi = '';

        if ($status == 1) {
            $judulNotifikasi = 'Pengajuan Disetujui';
            $pesanNotifikasi = 'Pengajuan bantuan alat Anda telah disetujui.';
        } elseif ($status == 0) {
            $judulNotifikasi = 'Pengajuan Ditolak';
            $pesanNotifikasi = 'Pengajuan bantuan alat Anda ditolak.';
        }

        if ($judulNotifikasi && $pesanNotifikasi) {
            event(new NotifGenerated(
                $user,
                $judulNotifikasi,
                $pesanNotifikasi,
                'laporan_bantuan_alat_status'
            ));
        }
    }

    public function export()
    {
        try {
            return new LaporanBantuanAlatExport();
        } catch (Throwable $e) {
            throw new DataAccessException("Gagal export data laporan hibat alat.", 0, $e);
        }
    }
}
