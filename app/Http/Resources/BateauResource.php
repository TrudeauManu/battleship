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
            'porte-avions' => $this->positions_porte_avions,
            'cuirasse' => $this->positions_cuirasse,
            'destroyer' => $this->positions_destroyer,
            'sous-marin' => $this->positions_sous_marin,
            'patrouilleur' => $this->positions_patrouilleur,
        ];
    }
}
