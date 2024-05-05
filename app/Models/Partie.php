<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Modèle d'une Partie.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class Partie extends Model
{
    use HasFactory;

    protected $fillable = ['adversaire'];

    /**
     * Retourne les missiles de la partie.
     *
     * @return HasMany
     */
    public function missiles(): HasMany
    {
        return $this->hasMany(Missile::class);
    }

    /**
     * Retourne les bateaux de la partie.
     *
     * @return HasOne
     */
    public function bateaux(): HasOne
    {
        return $this->hasOne(Bateaux::class);
    }

    /**
     * Retourne le user de la partie.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
