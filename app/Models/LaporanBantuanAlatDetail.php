<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBantuanAlatDetail extends Model
{
    protected $table = 'permintaan_bantuan_alat_detail';
    protected $fillable = [
        'permintaan_bantuan_alat_id',
        'nama_ketua',
        'no_hp_ketua',
        'npwp',
        'email_kelompok_tani',
        'password_email',
        'path_ktp_ketua',
        'path_badan_hukum',
        'path_piagam',
        'path_surat_domisili',
        'path_foto_lokasi',
        'path_ktp_sekretaris',
        'path_ktp_ketua_upkk',
        'path_ktp_anggota1',
        'path_ktp_anggota2'
    ];
    public function LaporanBantuanAlat()
    {
        return $this->belongsTo(LaporanBantuanAlat::class, 'permintaan_bantuan_alat_id', 'id');
    }
}
