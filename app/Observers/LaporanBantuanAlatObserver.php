<?php

namespace App\Observers;

use App\Models\LaporanBantuanAlat;
use App\Models\LaporanBantuanAlatDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LaporanBantuanAlatObserver
{
    public function created(LaporanBantuanAlat $laporan): void
    {
//        try {
//            $data = request()->all();
//            $now = date('Y-m-d');
//            Log::info($data["path_proposal"]);
//            $kelompokTaniId = $data['kelompok_tani_id'];
//            $fileFields = [
//                'path_ktp_ketua',
//                'path_ktp_sekretaris',
//                'path_ktp_ketua_upkk',
//                'path_ktp_anggota1',
//                'path_ktp_anggota2',
//                'path_badan_hukum',
//                'path_piagam',
//                'path_surat_domisili',
//                'path_foto_lokasi',
//                'path_proposal'
//            ];
//
//            $paths = [];
//
//            foreach ($fileFields as $field) {
//                if (request()->hasFile($field)) {
//                    $file = request()->file($field);
//                    $filename = time() . '_' . $file->getClientOriginalName();
//                    $paths[$field] = $file->storeAs("laporan_bantuan_alat/$kelompokTaniId/$now/$field", $filename, 'public');
//                } else {
//                    $paths[$field] = null;
//                }
//            }
//
//            LaporanBantuanAlatDetail::create([
//                'permintaan_bantuan_alat_id' => $laporan->id,
//                'nama_ketua' => $data['nama_ketua'],
//                'no_hp_ketua' => $data['no_hp_ketua'],
//                'npwp' => $data['npwp'],
//                'email_kelompok_tani' => $data['email_kelompok_tani'],
//                'password_email' => $data['password_email'],
//                'path_ktp_ketua' => $paths['path_ktp_ketua'],
//                'path_badan_hukum' => $paths['path_badan_hukum'],
//                'path_piagam' => $paths['path_piagam'],
//                'path_surat_domisili' => $paths['path_surat_domisili'],
//                'path_foto_lokasi' => $paths['path_foto_lokasi'],
//                'path_ktp_sekretaris' => $paths['path_ktp_sekretaris'],
//                'path_ktp_ketua_upkk' => $paths['path_ktp_ketua_upkk'],
//                'path_ktp_anggota1' => $paths['path_ktp_anggota1'],
//                'path_ktp_anggota2' => $paths['path_ktp_anggota2'],
//            ]);
//        } catch (\Throwable $th) {
//            Log::error('Gagal menyimpan laporan bantuan alat: ' . $th->getMessage());
//        }
    }

    /**
     * Handle the LaporanBantuanAlat "deleted" event.
     */
    public function deleted(LaporanBantuanAlat $laporan): void
    {
        $laporanDetail = LaporanBantuanAlatDetail::where('permintaan_bantuan_alat_id', $laporan->id)
            ->select([
                'id',
                'path_ktp_ketua',
                'path_ktp_sekretaris',
                'path_ktp_ketua_upkk',
                'path_ktp_anggota1',
                'path_ktp_anggota2',
                'path_badan_hukum',
                'path_piagam',
                'path_surat_domisili',
                'path_foto_lokasi',
            ])
            ->first();

        if ($laporanDetail) {
            $fileFields = [
                'path_ktp_ketua',
                'path_ktp_sekretaris',
                'path_ktp_ketua_upkk',
                'path_ktp_anggota1',
                'path_ktp_anggota2',
                'path_badan_hukum',
                'path_piagam',
                'path_surat_domisili',
                'path_foto_lokasi',
            ];

            foreach ($fileFields as $field) {
                if (!empty($laporanDetail->$field)) {
                    try {
                        Storage::disk('public')->delete($laporanDetail->$field);
                    } catch (\Throwable $th) {
                        Log::error("Gagal menghapus file {$field}: " . $th->getMessage());
                    }
                }
            }

            $laporanDetail->delete();
        }
    }

    /**
     * Handle the LaporanBantuanAlat "updated" event.
     */
    public function updated(LaporanBantuanAlat $laporan): void
    {
        //
    }

    /**
     * Handle the LaporanBantuanAlat "restored" event.
     */
    public function restored(LaporanBantuanAlat $laporan): void
    {
        //
    }

    /**
     * Handle the LaporanBantuanAlat "force deleted" event.
     */
    public function forceDeleted(LaporanBantuanAlat $laporan): void
    {
        //
    }
}
