<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanBantuanAlatDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nama_ketua' => $this->nama_ketua,
            'no_hp_ketua' => $this->no_hp_ketua,
            'npwp' => $this->npwp,
            'eamil_kelompok_tani' => $this->email_kelompok_tani,
            'ktp_ketua' => $this->path_ktp_ketua,
            'badan_hukum' => $this->path_badan_hukum,
            'piagam' => $this->path_piagam,
            'surat_domisili' => $this->path_surat_domisili,
            'foto_lokasi' => $this->path_foto_lokasi,
            'ktp_sekretaris' => $this->path_ktp_sekretaris,
            'ktp_ketua_upkk' => $this->path_ktp_ketua_upkk,
            'ktp_anggota1' => $this->path_ktp_anggota1,
            'ktp_anggota2' => $this->path_ktp_anggota2,
        ];
    }
}
