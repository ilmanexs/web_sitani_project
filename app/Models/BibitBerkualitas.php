<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibitBerkualitas extends Model
{
    protected $fillable = [
        'nama',
        'komoditas_id',
        'deskripsi',
    ];

    /**
     * Relasin one to one dengan model komoditas
     *
     * @return BelongsTo<Komoditas, BibitBerkualitas>
     */
    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }
}
