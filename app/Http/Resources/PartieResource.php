<?php

namespace App\Http\Resources;

use App\Models\Bateau;
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
        // TODO: reformate response
        return [
            'id' => $this->id,
            'adversaire' => $this->adversaire,
            //'bateaux' =>  BateauResource::collection($this->bateaux)
        ];
    }
}
