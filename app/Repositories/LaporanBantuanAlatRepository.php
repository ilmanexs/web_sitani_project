<?php

namespace App\Repositories;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\LaporanBantuanAlat;
use App\Models\LaporanBantuanAlatDetail;
use App\Repositories\Interfaces\PermintaanBantuanAlatRepositoryInterface;
use App\Trait\LoggingError;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class LaporanBantuanAlatRepository implements PermintaanBantuanAlatRepositoryInterface
{
    use LoggingError;

    /**
     * @param false $withRelations
     * @param array $criteria
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getAll($withRelations = false, array $criteria = []): Collection|array
    {
        try {
            $query = LaporanBantuanAlat::query();
            if ($withRelations) {
                $query->with([
                    'kelompokTani:id,nama',
                    'penyuluh:id,penyuluh_terdaftar_id',
                    'penyuluh.penyuluhTerdaftar:id,nama,no_hp',
                    'LaporanBantuanAlatDetail'
                ])->select(['id', 'kelompok_tani_id', 'penyuluh_id', 'status', 'alat_diminta', 'created_at'])->get();
            }
            return $query->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, [], "Terjadi kesalahan tak terduga di " . __METHOD__);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat get all data di repository.');
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getById($id): ?Model
    {
        try {
            return LaporanBantuanAlat::with([
                'kelompokTani:id,nama',
                'penyuluh:id,penyuluh_terdaftar_id',
                'penyuluh.penyuluhTerdaftar:id,nama,no_hp',
                'LaporanBantuanAlatDetail'
            ])->select(['id', 'kelompok_tani_id', 'penyuluh_id', 'status', 'created_at', 'path_proposal'])
                ->where('id', $id)
                ->first();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id], "Terjadi kesalahan tak terduga di " . __METHOD__);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat get data by id di repository.');
        }

    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function create(array $data): ?Model
    {
        try {
            $laporan = LaporanBantuanAlat::create($data);
            if (!$laporan) {
                throw new DataAccessException('Laporan permintaan bantuan alat gagal disimpan');
            }
            return $laporan;
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, $data, "Terjadi kesalahan tak terduga di " . __METHOD__);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat create data di repository.');
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function update(string|int $id, array $data): bool|int
    {
        try {
            $laporan = LaporanBantuanAlat::findOrFail($id);
            $result = $laporan->update($data);

            if (!$result) {
                $this->LogGeneralException(new \Exception('Gagal memperbarui laporan bantuan alat'));
            }

            return $result;
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id]);
            throw new ResourceNotFoundException('Laporan bantuan alat tidak ditemukan');
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id, 'data_baru' => $data]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data], "Terjadi kesalahan tak terduga di " . __METHOD__);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat update data di repository.');
        }
    }

    /**
     * @inheritDoc
     * @throws ResourceNotFoundException
     * @throws DataAccessException
     */
    public function delete(string|int $id): bool|int
    {
        try {
            $model = LaporanBantuanAlat::findOrFail($id);
            $result = $model->delete();

            if (!$result) {
                $this->LogGeneralException(new \Exception('Gagal menghapus laporan bantuan alat'));
            }

            return $result;
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id]);
            throw new ResourceNotFoundException('Laporan bantuan alat tidak ditemukan');
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id], "Terjadi kesalahan tak terduga di " . __METHOD__);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat update data di repository.');
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getAllByKecamatanId(int|string $id): Collection
    {
        try {
            return LaporanBantuanAlat::whereHas('kelompokTani', function ($query) use ($id) {
                $query->where('kecamatan_id', $id);
            })
                ->with([
                    'kelompokTani:id,nama,kecamatan_id,desa_id',
                    'kelompokTani.penyuluhTerdaftars:id,nama,no_hp,alamat',
                    'kelompokTani.kecamatan:id,nama',
                    'kelompokTani.desa:id,nama',
                    'LaporanBantuanAlatDetail'
                ])
                ->select(['id', 'kelompok_tani_id', 'penyuluh_id', 'status', 'alat_diminta', 'created_at', 'path_proposal'])
                ->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id], "Terjadi kesalahan tak terduga di " . __METHOD__);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getTotalByKecamatanId(int|string $id): int
    {
        try {
            $tahunskrng = Carbon::now()->year;
            return LaporanBantuanAlat::whereHas('kelompokTani', function ($query) use ($id) {
                $query->where('kecamatan_id', $id);
            })->whereYear('created_at', $tahunskrng)->count();
        } catch (QueryException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
        public function getStatsByKecamatanId(int|string|null $id): array
    {
        try {
            $statusDefault = [
                'rejected' => 0,
                'approved' => 0,
                'pending' => 0,
            ];

            $tahunskrng = Carbon::now()->year;

            $query = LaporanBantuanAlat::select('status', DB::raw('count(*) as total'))
                ->whereIn('status', [1, 2, 3])
                ->whereYear('created_at', $tahunskrng);

            if (!is_null($id)) {
                $query->whereHas('kelompokTani', function ($query) use ($id) {
                    $query->where('kecamatan_id', $id);
                });
            }

            $total = $query->groupBy('status')->pluck('total', 'status')->toArray();

            $mappingStatus = [
                0 => 'rejected',
                1 => 'approved',
                2 => 'pending',
            ];

            foreach ($mappingStatus as $statusValue => $statusName) {
                $statusDefault[$statusName] = $total[$statusValue] ?? 0;
            }
            return $statusDefault;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id], "Terjadi kesalahan tak terduga di " . __METHOD__);
            throw new DataAccessException('Terjadi kesalahan tak terduga di ' . __METHOD__);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function insertLaporanDetail(array $data): ?Model
    {
        try {
            return LaporanBantuanAlatDetail::create($data);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['data' => $data]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['data' => $data], "Terjadi kesalahan tak terduga di " . __METHOD__);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat insert laporan detail di repository.');
        }
    }
    public function getTahunTersedia()
    {
        return DB::table('permintaan_bantuan_alat')
            ->selectRaw('YEAR(updated_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
    }

    public function countPermintaanHibah(int $tahun)
    {
        return DB::table('permintaan_bantuan_alat')
            ->whereYear('updated_at', $tahun)
            ->count();
    }


    public function countDiterima(int $tahun)
    {
        return DB::table('permintaan_bantuan_alat')
            ->where('status', '1')
            ->whereYear('updated_at', $tahun)
            ->count();
    }

    public function countDitolak(int $tahun)
    {
        return DB::table('permintaan_bantuan_alat')
            ->where('status', '0')
            ->whereYear('updated_at', $tahun)
            ->count();
    }
}
