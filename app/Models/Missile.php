<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

/**
 * Modèle d'un Missile.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class Missile extends Model
{
    use HasFactory;

    protected $fillable = ['resultat'];

    /**
     * Retourne la partie dans laquelle le missile à été créer.
     *
     * @return BelongsTo
     */
    public function partie(): BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }
}
