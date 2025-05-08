<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanBantuanAlatResource extends JsonResource
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'alat_diminta' => $this->alat_diminta,
            'kelompok_tani' => new KelompokTaniResource($this->whenLoaded('kelompokTani')),
            'laporan_detail' => new LaporanBantuanAlatDetailResource($this->whenLoaded('LaporanBantuanAlatDetail')),
        ];
    }
}
