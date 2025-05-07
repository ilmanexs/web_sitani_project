<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanKondisiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'kelompok_tani_id' => $this->id,
            'nama_kelompok_tani' => $this->nama,
            'desa' => new DesaResource($this->whenLoaded('desa')),
            'laporan' => $this->laporanKondisi->map(function ($laporan) {
                return [
                    'id' => $laporan->id,
                    'status' => $laporan->status,
                    'created_at' => $laporan->created_at,
                    'komoditas' => new KomoditasResource($laporan->komoditas),
                    'pelapor' => [
                        'id' => $laporan->penyuluh->penyuluhTerdaftar->id,
                        'nama' => $laporan->penyuluh->penyuluhTerdaftar->nama,
                    ],
                    'detail' => new LaporanKondisiDetailResource($laporan->laporanKondisiDetail),
                ];
            })
        ];
    }
}
