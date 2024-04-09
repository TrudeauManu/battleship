<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bateau extends Model
{
    use HasFactory;

    protected $table = 'bateaux';

    public function partie(): BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }
}
