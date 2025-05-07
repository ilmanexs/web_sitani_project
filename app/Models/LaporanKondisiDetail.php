<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKondisiDetail extends Model
{
    protected $fillable = [
        'laporan_kondisi_id',
        'luas_lahan',
        'estimasi_panen',
        'jenis_bibit',
        'foto_bibit',
        'lokasi_lahan',
        'path_foto_lokasi',
    ];


    /**
     * Relasi one to one dengan model Laporan kondisi
     *
     * @return BelongsTo<LaporanKondisi, LaporanKondisiDetail>
     */
    public function laporanKondisi()
    {
        return $this->belongsTo(LaporanKondisi::class);
    }
}
