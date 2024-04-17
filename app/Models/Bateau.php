<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bateau extends Model
{
    use HasFactory;

    protected $table = 'bateaux';

    public function positions_porte_avions(): Attribute
    {
        return Attribute::make(
          get: fn ($value) => json_decode($value),
          set: fn ($value) => json_encode($value),
        );
    }

    public function positions_cuirasse(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function positions_sous_marin(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function positions_destroyer(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function positions_patrouilleur(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function partie(): BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }
}
