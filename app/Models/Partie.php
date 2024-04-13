<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Partie extends Model
{
    use HasFactory;
    protected $fillable = ['adversaire'];

    public function missiles(): HasMany
    {
        return $this->hasMany(Missile::class);
    }

    public function bateaux(): HasOne
    {
        return $this->hasOne(Bateau::class);
    }
}
