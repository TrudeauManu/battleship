<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartieResource extends JsonResource
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
            'adversaire' => $this->adversaire,
            'bateaux' => [
                'porte-avions' => json_decode($this->bateaux->positions_porte_avions),
                'cuirasse' => json_decode($this->bateaux->positions_cuirasse),
                'destroyer' => json_decode($this->bateaux->positions_destroyer),
                'sous-marin' => json_decode($this->bateaux->positions_sous_marin),
                'patrouilleur' => json_decode($this->bateaux->positions_patrouilleur),
            ],
            'created_at' => $this->created_at
        ];
    }
}
