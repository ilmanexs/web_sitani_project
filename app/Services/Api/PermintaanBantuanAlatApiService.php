<?php

namespace App\Services\Api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\PermintaanBantuanAlatRepositoryInterface;
use App\Services\Interfaces\PermintaanAlatApiServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PermintaanBantuanAlatApiService implements PermintaanAlatApiServiceInterface
{
    protected PermintaanBantuanAlatRepositoryInterface $repository;

    public function __construct(PermintaanBantuanAlatRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function getAllByKecamatanId(int|string $id): Collection
    {
        try {
            $laporans = $this->repository->getAllByKecamatanId($id);
            if ($laporans->isEmpty()) {
                throw new ResourceNotFoundException();
            }
            return $laporans;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat fetch data permintaan bantuan alat berdasarkan kecamatan id.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat fetch data permintaan bantuan alat berdasarkan kecamatan id.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     * @throws Throwable
     */
    public function create(array $data): ?Model
    {
        DB::beginTransaction();
        $pathProposal = null;
        $laporanModel = null;
        try {
            $now = Carbon::now()->format('Y-m-d');
            $kelompokTaniId = $data['kelompok_tani_id'];
            $fileProposal = $data['path_proposal'];
            if ($fileProposal && $fileProposal->isValid()) {
                $namaFileProposal = time() . '-' . $fileProposal->getClientOriginalName();
                $pathProposal = $fileProposal->storeAs('laporan_bantuan_alat/' . $now . '/path_proposal', $namaFileProposal, 'public');

                $laporanModel = $this->repository->create([
                    'kelompok_tani_id' => $kelompokTaniId,
                    'penyuluh_id' => $data['penyuluh_id'],
                    'alat_diminta' => $data['alat_diminta'],
                    'path_proposal' => $pathProposal,
                    'status' => '2',
                ]);

                if ($laporanModel === null) {
                    throw new DataAccessException('Gagal menyimpan laporan bantuan alat di repository.');
                }

                $detailMap = [
                    'path_ktp_ketua' => $data['path_ktp_ketua'],
                    'path_ktp_sekretaris' => $data['path_ktp_sekretaris'],
                    'path_ktp_ketua_upkk' => $data['path_ktp_ketua_upkk'],
                    'path_ktp_anggota1' => $data['path_ktp_anggota1'],
                    'path_ktp_anggota2' => $data['path_ktp_anggota2'],
                    'path_badan_hukum' => $data['path_badan_hukum'],
                    'path_piagam' => $data['path_piagam'],
                    'path_surat_domisili' => $data['path_surat_domisili'],
                    'path_foto_lokasi' => $data['path_foto_lokasi'],
                ];

                $pathDetail = [];

                try {
                    foreach ($detailMap as $key => $file) {
                        if ($file instanceof UploadedFile && $file->isValid()) {
                            $namaFile = time() . '-' . $file->getClientOriginalName();
                            $pathDetail[$key] = $file->storeAs('laporan_bantuan_alat/' . $now . '/' . $key, $namaFile, 'public');
                        }
                    }

                    $detailLaporan = $this->repository->insertLaporanDetail([
                        'permintaan_bantuan_alat_id' => $laporanModel->id,
                        'nama_ketua' => $data['nama_ketua'],
                        'no_hp_ketua' => $data['no_hp_ketua'],
                        'npwp' => $data['npwp'],
                        'email_kelompok_tani' => $data['email_kelompok_tani'],
                        'password_email' => $data['password_email'],
                        'path_ktp_ketua' => $pathDetail['path_ktp_ketua'],
                        'path_badan_hukum' => $pathDetail['path_badan_hukum'],
                        'path_piagam' => $pathDetail['path_piagam'],
                        'path_surat_domisili' => $pathDetail['path_surat_domisili'],
                        'path_foto_lokasi' => $pathDetail['path_foto_lokasi'],
                        'path_ktp_sekretaris' => $pathDetail['path_ktp_sekretaris'],
                        'path_ktp_ketua_upkk' => $pathDetail['path_ktp_ketua_upkk'],
                        'path_ktp_anggota1' => $pathDetail['path_ktp_anggota1'],
                        'path_ktp_anggota2' => $pathDetail['path_ktp_anggota2'],
                    ]);

                    if ($detailLaporan === null) {
                        throw new DataAccessException('Gagal menyimpan detail laporan bantuan alat di repository.');
                    }
                } catch (DataAccessException $e) {
                    DB::rollBack();
                    $this->deleteFile([...$pathDetail]);
                    throw $e;
                } catch (Throwable $e) {
                    DB::rollBack();
                    $this->deleteFile([...$pathDetail]);
                    throw new DataAccessException('Terjadi kesalahan tak terduga saat menyimpan detail laporan bantuan alat.', 0, $e);
                }
            }

            DB::commit();
            return $laporanModel->load('LaporanBantuanAlatDetail');
        } catch (QueryException $e) {
            DB::rollBack();
            $this->deleteFile([$pathProposal]);
            throw new DataAccessException('Database error saat menyimpan data permintaan bantuan alat.', 0, $e);
        } catch (DataAccessException $e) {
            DB::rollBack();
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menyimpan data permintaan bantuan alat.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getTotalByKecamatanId(int|string $id): int
    {
        try {
            return $this->repository->getTotalByKecamatanId($id);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghitung total permintaan bantuan alat berdasarkan kecamatan id.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung total permintaan bantuan alat berdasarkan kecamatan id.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @throws DataAccessException
     */
    public function getStatsByKecamatanId(int|string $id): array
    {
        try {
            return $this->repository->getStatsByKecamatanId($id);
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat menghitung statistik permintaan bantuan alat berdasarkan kecamatan id.', 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghitung statistik permintaan bantuan alat berdasarkan kecamatan id.', 0, $e);
        }
    }

    /**
     * Menghapus file berdasarkan path yang diberikan
     *
     * @param array $path path file
     * @return void
     */
    private function deleteFile(array $path): void
    {
        foreach ($path as $file) {
            if (isset($file) && Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}
