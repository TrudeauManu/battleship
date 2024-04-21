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

        $tableProbabilite = array_fill(0, $TAILLE_TABLEAU, array_fill(0, $TAILLE_TABLEAU, 0));

        // TODO: doit etre reactif avec le board ennemi.
        $bateaux = [
            'porte-avions' => 5,
            'cuirasse' => 4,
            'destroyer' => 3,
            'sous-marin' => 3,
            'patrouilleur' => 2
        ];

        // TODO: Regarder missiles et updater le tableau avec miss et hit et couler
        $tableProbabilite[5][5] = -1;
        $tableProbabilite[4][4] = -1;

        $this->monteCarlo($tableProbabilite, $bateaux, $TAILLE_TABLEAU, 100000);

        // TODO: A desactiver si bateaux couler?
        $this->boostProbabiliteAdjacentHit($tableProbabilite, $TAILLE_TABLEAU);

        return $this->trouverPlusProbable($tableProbabilite, $TAILLE_TABLEAU);
    }

    private function calculateBateauxConfigurations(array &$tableProbabilite, array $bateaux, int $taille_tableau): void
    {
        $tableau = array_fill(0, $taille_tableau, array_fill(0, $taille_tableau, 0));
        foreach ($bateaux as $bateau => $longueur) {
            $placer = false;
            $estHorizontal = rand(0, 1);
            while (!$placer) {
                if ($estHorizontal) {
                    $row = rand(0, $taille_tableau - 1);
                    $col = rand(0, $taille_tableau - $longueur);
                } else {
                    $row = rand(0, $taille_tableau - $longueur);
                    $col = rand(0, $taille_tableau - 1);
                }
                $placer = !$this->overlapped($tableau, $row, $col, $longueur, $estHorizontal, $tableProbabilite);
                if ($placer) {
                    for ($i = 0; $i < $longueur; $i++) {
                        // TODO Ya un stupid bug.. rajoute + 1 une fois random
                        if ($tableProbabilite[$estHorizontal ? $row : $row + $i][$estHorizontal ? $col + $i : $col] >= 0)
                            $tableProbabilite[$estHorizontal ? $row : $row + $i][$estHorizontal ? $col + $i : $col] += 1;
                    }
                }
            }
        }
    }

    function overlapped(array $tableau, int $row, int $col, int $longueur, bool $estHorizontal, array $tableProbabilite): bool {
        for ($i = 0; $i < $longueur; $i++) {
            if ($estHorizontal && $tableProbabilite[$row][$col + $i] === -1)
                return true;
            if (!$estHorizontal && $tableProbabilite[$row + $i][$col] === -1)
                return true;
            if ($estHorizontal && $tableau[$row][$col + $i] === 1)
                return true;
            if (!$estHorizontal && $tableau[$row + $i][$col] === 1)
                return true;
        }
        return false;
    }

    function boostProbabiliteAdjacentHit($tableProbabilite, int $taille_tableau): void {
        for ($i = 0; $i < $taille_tableau; $i++) {
            for ($j = 0; $j < $taille_tableau; $j++) {
                if ($tableProbabilite[$i][$j] === -2) {
                    $tableProbabilite[$i + 1][$j] *= 2;
                    $tableProbabilite[$i][$j + 1] *= 2;
                    $tableProbabilite[$i - 1][$j] *= 2;
                    $tableProbabilite[$i][$j - 1] *= 2;
                }
            }
        }
    }

    function monteCarlo(array &$tableProbabilite, array $bateaux, int $taille_tableau, int $nb_iteration): void {
        for ($i = 0; $i < $nb_iteration; $i++) {
            $this->calculateBateauxConfigurations($tableProbabilite, $bateaux, $taille_tableau);
        }
    }

    function trouverPlusProbable(array $tableProbabilite, int $taille_tableau): string {
        $plusProbable = 0;
        $row = 0;
        $col = 0;
        for ($i = 0; $i < $taille_tableau; $i++) {
            for ($j = 0; $j < $taille_tableau; $j++) {
                if ($tableProbabilite[$i][$j] > $plusProbable) {
                    $plusProbable = $tableProbabilite[$i][$j];
                    $row = $i;
                    $col = $j;
                }
            }
        }
        return chr(65 + $row) . "-" . ($col + 1);
    }
}
