<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ressource de missile.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class MissileResource extends JsonResource
{
    /**
     * Transforme la resource en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'coordonnee' => $this->coordonnee,
            'resultat' => $this->resultat,
            'created_at' => $this->created_at
        ];
    }
}
