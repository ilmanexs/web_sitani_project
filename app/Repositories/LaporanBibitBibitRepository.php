<?php

namespace App\Repositories;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\KelompokTani;
use App\Models\LaporanKondisi;
use App\Models\LaporanKondisiDetail;
use App\Repositories\Interfaces\LaporanBibitRepositoryInterface;
use App\Trait\LoggingError;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class LaporanBibitBibitRepository implements LaporanBibitRepositoryInterface
{
    use LoggingError;

    /**
     * @inheritDoc
     * @param bool $withRelations
     * @param array $criteria
     * @return Collection|array
     * @throws DataAccessException
     */
    public function getAll(bool $withRelations = false, array $criteria = []): Collection|array
    {
        try {
            $query = LaporanKondisi::query();
            if ($withRelations) {
                $query->with([
                    'kelompokTani' => fn($q) => $q->select(['id', 'nama']),
                    'komoditas' => fn($q) => $q->select(['id', 'nama', 'musim']),
                    'penyuluh' => fn($q) => $q->select(['id', 'penyuluh_terdaftar_id'])->with([
                        'penyuluhTerdaftar' => fn($q2) => $q2->select(['id', 'nama', 'no_hp']),
                    ]),
                    'laporanKondisiDetail' => fn($q) => $q->select(['id', 'laporan_kondisi_id', 'luas_lahan', 'estimasi_panen', 'jenis_bibit', 'foto_bibit', 'lokasi_lahan']),
                ]);
            }
            return $query->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat fetch data laporan.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return Model|null
     * @throws DataAccessException
     */
    public function getById(string|int $id): ?Model
    {
        try {
            $query = LaporanKondisi::query();
            $query->with([
                'kelompokTani' => fn($q) => $q->select(['id', 'nama', 'desa_id', 'kecamatan_id'])->with([
                    'desa' => fn($q2) => $q2->select(['id', 'nama']),
                    'kecamatan' => fn($q2) => $q2->select(['id', 'nama']),
                ]),
                'komoditas' => fn($q) => $q->select(['id', 'nama', 'musim']),
                'penyuluh' => fn($q) => $q->select(['id', 'penyuluh_terdaftar_id'])->with([
                    'penyuluhTerdaftar' => fn($q2) => $q2->select(['id', 'nama', 'no_hp']),
                ]),
                'laporanKondisiDetail' => fn($q) => $q->select(['id', 'laporan_kondisi_id', 'luas_lahan', 'estimasi_panen', 'jenis_bibit', 'foto_bibit', 'lokasi_lahan', 'path_foto_lokasi']),
            ]);

            return $query->find($id);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat fetch data laporan dengan id: ' . $id, 0, $e);
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
            return LaporanKondisi::create($data);
        } catch (QueryException $e) {
            $this->LogSqlException($e, $data);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, $data);
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menyimpan laporan bibit.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @param array $data
     * @return bool|int
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function update(string|int $id, array $data): bool|int
    {
        try {
            $model = LaporanKondisi::findOrFail($id);
            $result = $model->update($data);

            if (!$result) {
                $this->LogGeneralException(new \Exception("Gagal memperbarui data laporan bibit."), ['id' => $id, 'data' => $data]);
            }

            return $result;
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id]);
            throw new ResourceNotFoundException("Laporan bibit dengan id {$id} tidak ditemukan untuk diperbarui.", 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id, 'data_baru' => $data]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data]);
            throw new DataAccessException('Terjadi kesalahan saat memperbarui data laporan bibit.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return bool|int
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function delete(string|int $id): bool|int
    {
        try {
            $model = LaporanKondisi::findOrFail($id);
            $result = $model->delete();

            if (!$result) {
                $this->LogGeneralException(new \Exception("Gagal menghapus laporan bibit."), ['id' => $id]);
            }

            return (bool)$result;
        } catch (ModelNotFoundException $e) {
            $this->LogNotFoundException($e, ['id' => $id]);
            throw new ResourceNotFoundException("Laporan bibit dengan id {$id} tidak ditemukan untuk dihapus.", 0, $e);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id]);
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghapus laporan bibit.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $kecamatanId
     * @return Collection|array
     * @throws DataAccessException
     */
    public function getByKecamatanId(string|int $kecamatanId): Collection|array
    {
        try {
            return KelompokTani::where('kecamatan_id', $kecamatanId)->with([
                'desa:id,nama',
                'kecamatan:id,nama',
                'laporanKondisi:id,penyuluh_id,komoditas_id,kelompok_tani_id,status,created_at',
                'laporanKondisi.komoditas:id,nama',
                'laporanKondisi.penyuluh.penyuluhTerdaftar:id,nama',
                'laporanKondisi.laporanKondisiDetail:id,laporan_kondisi_id,luas_lahan,estimasi_panen,jenis_bibit,foto_bibit,lokasi_lahan,path_foto_lokasi',
            ])->select(['id', 'nama', 'desa_id', 'kecamatan_id'])->get();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Unexpected repository error in getByPenyuluhId.', 0, $e);
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
            return LaporanKondisi::count();
        } catch (QueryException $e) {
            $this->LogSqlException($e);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e);
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghitung total laporan bibit.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param int|null $kecamatanId
     * @return array
     * @throws DataAccessException
     */
    public function getLaporanStatusCounts(?int $kecamatanId = null): array
    {
        $statusCounts = [
            'rejected' => 0,
            'approved' => 0,
            'pending' => 0,
        ];

        try {
            $currentYear = Carbon::now()->year;

            $query = LaporanKondisi::select('status', DB::raw('count(*) as total'))
                ->whereIn('status', [1, 2, 3])
                ->whereYear('created_at', $currentYear);

            if (!is_null($kecamatanId)) {
                $query->whereHas('kelompokTani', function ($query) use ($kecamatanId) {
                    $query->where('kecamatan_id', $kecamatanId);
                });
            }

            $rawCounts = $query->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $statusMap = [
                0 => 'rejected',
                1 => 'approved',
                2 => 'pending',
            ];

            foreach ($statusMap as $statusValue => $statusName) {
                $statusCounts[$statusName] = $rawCounts[$statusValue] ?? 0;
            }
            return $statusCounts;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['penyuluh_id' => $kecamatanId]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['penyuluh_id' => $kecamatanId]);
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghitung total laporan berdasarkan statusnya.', 0, $e);
        }
    }

    /**
     * @param LaporanKondisi $laporan
     * @param array $data
     * @inheritDoc
     * @throws DataAccessException
     */
    public function insertDetailLaporan(array $data): ?Model
    {
        try {
            return LaporanKondisiDetail::create([
                'laporan_kondisi_id' => $data['laporan_id'],
                'luas_lahan' => $data['luas_lahan'],
                'estimasi_panen' => $data['estimasi_panen'],
                'jenis_bibit' => $data['jenis_bibit'],
                'foto_bibit' => $data['path_bibit'],
                'lokasi_lahan' => $data['lokasi_lahan'],
                'path_foto_lokasi' => $data['path_lokasi'],
            ]);
        } catch (QueryException $e) {
            $this->LogSqlException($e, $data);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, $data);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menyimpan detail laporan.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getTotalByKecamatanId(int|string $kecamatanId): int
    {
        try {
            $currentYear = Carbon::now()->year;

            return LaporanKondisi::whereHas('kelompokTani', function ($query) use ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            })
                ->whereYear('created_at', $currentYear)
                ->count();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['kecamatan_id' => $kecamatanId]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['kecamatan_id' => $kecamatanId]);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menyimpan detail laporan.', 0, $e);
        }
    }

    /**
     * @return Collection
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getTotalLuasLahan(): Collection
    {
        try {
            $currentYear = Carbon::now()->year;
            $fiveYearsAgo = $currentYear - 4;

            return LaporanKondisiDetail::whereBetween('created_at', [
                Carbon::create($fiveYearsAgo, 1, 1)->startOfYear(),
                Carbon::create($currentYear, 12, 31)->endOfYear(),
            ])
                ->selectRaw('YEAR(created_at) as tahun, SUM(luas_lahan) as total_luas_lahan')
                ->groupBy('tahun')
                ->orderBy('tahun', 'DESC')
                ->get()
                ->map(function ($item) {
                    return [
                        'tahun' => (int) $item->tahun,
                        'total_luas_lahan' => (float) $item->total_luas_lahan,
                    ];
                });
        } catch (QueryException $e) {
            $this->logSqlException($e);
            throw new DataAccessException('Terjadi kesalahan saat mengambil total luas lahan per tahun.', 0, $e);
        } catch (Throwable $e) {
            $this->logGeneralException($e);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat mengambil total luas lahan per tahun.', 0, $e);
        }
    }
}

