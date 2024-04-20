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

    public function createMissile(Partie $partie): Missile
    {
        $coordonnee = $this->calculateProbabilityMap();

        $missile = new Missile();
        $missile->coordonnee = $coordonnee;
        $missile->resultat = null;
        $missile->partie_id = $partie->id;
        $missile->save();

        return $missile;
    }

    private function calculateProbabilityMap(): string
    {
        $TAILLE_TABLEAU = 10;
        $bateaux = [
            'porte-avions' => 5,
            'cuirasse' => 4,
            'destroyer' => 3,
            'sous-marin' => 3,
            'patrouilleur' => 2
        ];

        $tableProbabilite = array_fill(0, $TAILLE_TABLEAU, array_fill(0, $TAILLE_TABLEAU, 0));

        for ($i = 0; $i < 50000; $i++) {
            $this->calculateBateauxConfigurations($tableProbabilite, $bateaux, $TAILLE_TABLEAU);
        }
        dd($tableProbabilite);

        return "allo";
    }

    private function calculateBateauxConfigurations(array &$tableProbabilite, array $bateaux, int $taille_tableau)
    {
        $tableau = array_fill(0, $taille_tableau, array_fill(0, $taille_tableau, 0));
        foreach ($bateaux as $bateau => $longueur) {
            $placer = false;
            $estHorizontal = rand(0,1);
            while (!$placer) {
                if ($estHorizontal) {
                    $row = rand(0, $taille_tableau - 1);
                    $col = rand(0, $taille_tableau - $longueur);
                } else {
                    $row = rand(0, $taille_tableau - $longueur);
                    $col = rand(0, $taille_tableau - 1);
                }
                $placer = !$this->overlapped($tableau, $row, $col, $longueur, $estHorizontal);
                if ($placer) {
                    for ($i = 0; $i < $longueur; $i++) {
                        $tableProbabilite[$estHorizontal ? $row : $row + $i][$estHorizontal ? $col + $i : $col] += 1;
                    }
                }
            }
        }
    }

    function overlapped($tableau, $row, $col, $longueur, $estHorizontal): bool {
        for ($i = 0; $i < $longueur; $i++) {
            if ($estHorizontal && $tableau[$row][$col + $i] === 1)
                return true;
            if (!$estHorizontal &&$tableau[$row + $i][$col] === 1)
                return true;
        }
        return false;
    }
}
