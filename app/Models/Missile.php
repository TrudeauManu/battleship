<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class Missile extends Model
{
    use HasFactory;

    protected $fillable = ['resultat'];

    public function partie(): BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }
}
