<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Collection de parties.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class PartieCollection extends ResourceCollection
{
    /**
     * Transforme la resource collection en un array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
