<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Missile extends Model
{
    use HasFactory;
    protected $fillable = ['resultat'];

    public function partie(): BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }

    public function createMissile(Partie $partie)
    {
        $coordonnee = $this->generateRandomCoordonnee();

        $missile = new Missile();
        $missile->coordonnee = $coordonnee;
        $missile->resultat = null;
        $missile->save();

        return $missile;
    }

    private function generateRandomCoordonnee(): string
    {
        $lettre = chr(rand(65,74));
        $nombre = rand(1,10);
        return $lettre . '-' . $nombre;
    }
}
