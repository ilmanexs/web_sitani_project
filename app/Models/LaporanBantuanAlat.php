<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBantuanAlat extends Model
{
    protected $table = 'permintaan_bantuan_alat';
    protected $fillable = [
        'kelompok_tani_id',
        'penyuluh_id',
        'status',
        'alat_diminta',
        'path_proposal'
    ];

    //Relasi One to One
    public function LaporanBantuanAlatDetail()
    {
        return $this->hasOne(LaporanBantuanAlatDetail::class, 'permintaan_bantuan_alat_id', 'id');
    }
    //Relasi One to Many
    public function KelompokTani()
    {
        return $this->belongsTo(KelompokTani::class);
    }
    //Relasi One to Many
    public function Penyuluh()
    {
        return $this->belongsTo(Penyuluh::class);
    }

}
