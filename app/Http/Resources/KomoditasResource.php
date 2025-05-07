<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KomoditasResource extends JsonResource
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
            'bibit_berkualitas' => $this->whenLoaded('bibitBerkualitas', fn() => BibitBerkualitas::collection($this->bibitBerkualitas)),
        ];
    }
}
