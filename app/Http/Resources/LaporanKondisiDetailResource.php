<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanKondisiDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'luas_lahan' => $this->luas_lahan,
            'estimasi_panen' => $this->estimasi_panen,
            'jenis_bibit' => $this->jenis_bibit,
            'foto_bibit' => $this->foto_bibit,
            'lokasi_lahan' => $this->lokasi_lahan,
            'path_foto_lokasi' => $this->path_foto_lokasi,
        ];
    }
}
