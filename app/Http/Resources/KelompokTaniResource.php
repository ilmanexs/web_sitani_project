<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KelompokTaniResource extends JsonResource
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
            'nama' => $this->nama,
            'desa' => new DesaResource($this->whenLoaded('desa')),
            'kecamatan' => new KecamatanResource($this->whenLoaded('kecamatan')),
            'penyuluhs' => $this->penyuluhTerdaftars->map(function ($penyuluh) {
                return [
                    'id' => $penyuluh->id,
                    'nama' => $penyuluh->nama,
                    'no_hp' => $penyuluh->no_hp,
                    'alamat' => $penyuluh->alamat,
                ];
            }),
        ];
    }
}
