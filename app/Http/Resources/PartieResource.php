<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource d'une partie.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
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
            'bateaux' => BateauResource::make($this->bateaux),
            'created_at' => $this->created_at
        ];
    }
}
