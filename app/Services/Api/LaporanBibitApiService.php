<?php

namespace App\Services\Api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\LaporanKondisiDetail;
use App\Repositories\Interfaces\LaporanBibitRepositoryInterface;
use App\Services\Interfaces\LaporanBibitApiServiceInterface;
use App\Trait\LoggingError;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class LaporanBibitApiService implements LaporanBibitApiServiceInterface
{
    use LoggingError;

    protected LaporanBibitRepositoryInterface $repository;

    public function __construct(LaporanBibitRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @param array $data
     * @return Model
     * @throws DataAccessException
     * @throws Throwable
     */
    public function create(array $data): Model
    {
        DB::beginTransaction();
        try {
            $laporan = $this->repository->create([
                'kelompok_tani_id' => $data['kelompok_tani_id'],
                'komoditas_id' => $data['komoditas_id'],
                'penyuluh_id' => $data['penyuluh_id'],
                'status' => $data['status'] ?? '2',
            ]);

            if ($laporan === null) {
                throw new DataAccessException('Gagal menyimpan laporan bibit di repository.');
            }
            $fotoBibit = $data['foto_bibit'] ?? null;
            $fotoLokasi = $data['foto_lokasi'] ?? null;

            if ($fotoBibit && $fotoLokasi && $fotoBibit->isValid() && $fotoLokasi->isValid()) {
                    $now = date('Y-m-d');
                    $fileBibit = $fotoBibit->getClientOriginalName();
                    $fileLokasi = $fotoLokasi->getClientOriginalName();
                    $waktuPanen = Carbon::parse($data['estimasi_panen'])->format('Y-m-d');
                    $pathBibit = $fotoBibit->storeAs('laporan_bibit/' . $data['kelompok_tani_id'] . '/' . $now, Str::random(10) . $fileBibit, 'public');
                    $pathLokasi = $fotoLokasi->storeAs('laporan_bibit/' . $data['kelompok_tani_id'] . '/' . $now,  Str::random(10) . $fileLokasi, 'public');
                    $detail = [
                        'laporan_id' => $laporan->id,
                        'luas_lahan' => $data['luas_lahan'],
                        'estimasi_panen' => $waktuPanen,
                        'jenis_bibit' => $data['jenis_bibit'],
                        'path_bibit' => $pathBibit,
                        'lokasi_lahan' => $data['lokasi_lahan'],
                        'path_lokasi' => $pathLokasi,
                    ];
                try {
                    $laporanDetail = $this->repository->insertDetailLaporan($detail);
                    if ($laporanDetail === null) {
                        throw new DataAccessException('Gagal menyimpan detail laporan bibit di repository.');
                    }
                } catch (QueryException $e) {
                    $this->deleteFile([$pathBibit, $pathLokasi]);
                    DB::rollBack();
                    throw new DataAccessException('Database error saat menyimpan detail laporan bibit di repository.');
                } catch (DataAccessException $e) {
                    DB::rollBack();
                    $this->deleteFile([$pathBibit, $pathLokasi]);
                    throw $e;
                } catch (Throwable $fileError) {
                    $this->deleteFile([$pathBibit, $pathLokasi]);
                    DB::rollBack();
                    throw new DataAccessException('Terjadi kesalahan tidak terduga saat menyimpan detail laporan bibit.', 0, $fileError);
                }
            }
            DB::commit();
            $laporan->load('laporanKondisiDetail');
            return $laporan;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new DataAccessException('Database error saat menyimpan data laporan bibit.', 0, $e);
        } catch (DataAccessException $e) {
            DB::rollBack();
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menyimpan data laporan bibit.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $penyuluhId
     * @return Collection
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function getByKecamatanId(string|int $penyuluhId): Collection
    {
        try {
            $laporan = $this->repository->getByKecamatanId($penyuluhId);
            if ($laporan->isEmpty()) {
                throw new ResourceNotFoundException('Laporan Bibit tidak ditemukan');
            }
            return $laporan;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data laporan bibit berdasarkan penyuluh id.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat fetch data laporan bibit berdasarkan penyuluh id.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string|int $kecamatanId
     * @return array
     * @throws DataAccessException
     */
    public function getLaporanStatusCounts(string|int $kecamatanId): array
    {
        try {
            return $this->repository->getLaporanStatusCounts($kecamatanId);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghitung total laporan bibit berdasarkan statusnya.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghitung total laporan bibit berdasarkan statusnya.', 0, $e);
        }
    }

    private function deleteFile(array $path): void
    {
        foreach ($path as $file) {
            if (isset($file) && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function getTotalByKecamatanId(int|string $kecamatanId): int
    {
        try {
            $total = $this->repository->getTotalByKecamatanId($kecamatanId);

            if ($total === 0) {
                throw new ResourceNotFoundException('Laporan pada kecamatan: ' . $kecamatanId . ' tidak ditemukan');
            }

            return $total;
        } catch (QueryException $e) {
            throw new DataAccessException('Terjadi kesalahan saat menghitung total laporan.');
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat mengambil total laporan berdasarkan kecamatan id');
        }
    }

    public function getTotalLuasLahan(): Collection
    {
        try {
            $total = $this->repository->getTotalLuasLahan();
            if ($total->isEmpty()) {
                throw new DataAccessException('Tidak ada data luas lahan dalam 5 tahun terakhir.');
            }
            return $total;
        } catch (QueryException $e) {
            throw new DataAccessException('Terjadi kesalahan saat menghitung total luas lahan dalam 5 tahun.');
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tidak terduga saat menghitung total luas lahan.');
        }
    }
}
