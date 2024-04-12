<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partie extends Model
{
    use HasFactory;
    protected $fillable = ['adversaire'];

    public function missiles(): HasMany
    {
        return $this->hasMany(Missile::class);
    }

    public function getBateauxAttribute(): array
    {
        return $this->bateaux()->get()->toArray();
    }

    public function bateaux(): HasMany
    {
        return $this->hasMany(Bateau::class);
    }
}
