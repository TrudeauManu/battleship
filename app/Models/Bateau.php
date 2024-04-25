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

    public function partie(): BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }
}
