<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle d'un set de bateaux.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class Bateaux extends Model
{
    use HasFactory;

    protected $table = 'bateaux';

    /**
     * Get les attributs qui doivent être cast.
     *
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'positions_porte_avions' => 'array',
            'positions_cuirasse' => 'array',
            'positions_destroyer' => 'array',
            'positions_sous_marin' => 'array',
            'positions_patrouilleur' => 'array',
        ];
    }

    /**
     * Get la partie dans laquelle le set de bateaux à été créer.
     *
     * @return BelongsTo
     */
    public function partie(): BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }
}
