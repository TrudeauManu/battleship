<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BateauResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'porte-avions' => json_decode($this->positions_porte_avions),
            'cuirasse' => json_decode($this->positions_cuirasse),
            'destroyer' => json_decode($this->positions_destroyer),
            'sous-marin' => json_decode($this->positions_sous_marin),
            'patrouilleur' => json_decode($this->positions_patrouilleur),
        ];
    }
}
