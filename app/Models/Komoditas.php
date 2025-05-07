<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Komoditas extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'musim',
    ];

    /**
     * Relasi one to many dengan model bibit berkualitas
     *
     * @return HasMany|Komoditas
     */
    public function bibitBerkualitas(): HasMany|Komoditas
    {
        return $this->hasMany(BibitBerkualitas::class);
    }

    /**
     * Relasi one to many dengan model laporan kondisi
     *
     * @return HasMany<LaporanKondisi, Komoditas>
     */
    public function laporanKondisi()
    {
        return $this->hasMany(LaporanKondisi::class);
    }
}
