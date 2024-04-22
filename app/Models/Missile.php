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
        $coordonnee = $this->calculateProbabilityMap($partie);

        $missile = new Missile();
        $missile->coordonnee = $coordonnee;
        $missile->resultat = null;
        $missile->partie_id = $partie->id;
        $missile->save();

        return $missile;
    }

    private function calculateProbabilityMap(Partie $partie): string
    {
        $TAILLE_TABLEAU = 10;

        $tableProbabilite = array_fill(0, $TAILLE_TABLEAU, array_fill(0, $TAILLE_TABLEAU, 0));

        $bateaux = [
            'porte-avions' => 5,
            'cuirasse' => 4,
            'destroyer' => 3,
            'sous-marin' => 3,
            'patrouilleur' => 2
        ];

        $this->updateTableProbabiliteEtBateaux($bateaux, $tableProbabilite, $partie);
        $dernierMissileATouche = $partie->missiles()->latest()['resultat'] === 1;

        $this->monteCarlo($tableProbabilite, $bateaux, $TAILLE_TABLEAU, $dernierMissileATouche, 100000);

        // TODO: A desactiver si bateaux couler?
        $this->boostProbabiliteAdjacentHit($tableProbabilite, $TAILLE_TABLEAU);
        dd($tableProbabilite);


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

    private function overlapped(array $tableau, int $row, int $col, int $longueur, bool $estHorizontal, array $tableProbabilite): bool {
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

    private function boostProbabiliteAdjacentHit($tableProbabilite, int $taille_tableau): void {
        for ($i = 0; $i < $taille_tableau; $i++) {
            for ($j = 0; $j < $taille_tableau; $j++) {
                if ($tableProbabilite[$i][$j] === -2) {
                    $tableProbabilite[$i + 1][$j] *= 3;
                    $tableProbabilite[$i][$j + 1] *= 3;
                    $tableProbabilite[$i - 1][$j] *= 3;
                    $tableProbabilite[$i][$j - 1] *= 3;
                }
            }
        }
    }

    private function monteCarlo(array &$tableProbabilite, array $bateaux, int $taille_tableau, bool $dernierMissileEstHit, int $nb_iteration): void {
        for ($i = 0; $i < $nb_iteration; $i++) {
            $this->calculateBateauxConfigurations($tableProbabilite, $bateaux, $taille_tableau);
        }
    }

    private function trouverPlusProbable(array $tableProbabilite, int $taille_tableau): string {
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

    private function updateTableProbabiliteEtBateaux(array &$bateaux, array &$tableProbabilite, Partie $partie): void
    {
        $missilesLancer = $partie->missiles()->get()->toArray();

        for ($i = 0; $i < count($missilesLancer); $i++) {
            $resultat = $missilesLancer[$i]['resultat'];
            $indexes = $this->convertCoordonneesToIndex($missilesLancer[$i]['coordonnee']);
            $row = $indexes['row'];
            $col = $indexes['col'];
            $resultat = $resultat === null ? -1 : $resultat;

            switch ($resultat) {
                case 0:
                    $tableProbabilite[$row][$col] = -1;
                    break;
                case 1:
                    $tableProbabilite[$row][$col] = -2;
                    break;
                case 2:
                    unset($bateaux['porte-avions']);
                    break;
                case 3:
                    unset($bateaux['cuirasse']);
                    break;
                case 4:
                    unset($bateaux['destroyer']);
                    break;
                case 5:
                    unset($bateaux['sous-marin']);
                    break;
                case 6:
                    unset($bateaux['patrouilleur']);
                    break;
                case -1:
                    break;
            }
        }
    }

    private function convertCoordonneesToIndex(string $coordonnee): array {
        $lettre = substr($coordonnee, 0, 1);
        $row = ord($lettre) - ord('A');
        $col = intval(substr($coordonnee, 2)) - 1;
        return ['row' => $row, 'col' => $col];
    }
}
