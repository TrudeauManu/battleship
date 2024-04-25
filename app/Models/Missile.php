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

        $this->calculateBateauxConfigurations($tableProbabilite, $bateaux, $TAILLE_TABLEAU);

        return $this->trouverPlusProbable($tableProbabilite, $TAILLE_TABLEAU);
    }

    private function calculateBateauxConfigurations(array &$tableProbabilite, array $bateaux, int $taille_tableau): void
    {
        foreach ($bateaux as $bateau => $longueur) {
           $this->passerDansLesCase($tableProbabilite, $longueur, $taille_tableau);
        }
    }

    private function passerDansLesCase(array &$tableProbabilite, int $longueurBateau, int $taille_tableau): void {
        for ($i = 0; $i < $taille_tableau; $i++) {
            for ($j = 0; $j < $taille_tableau; $j++) {
                $this->changerOrientationBateau($i, $j, $longueurBateau, $tableProbabilite);
            }
        }
    }

    private function changerOrientationBateau(int $row, int $col, int $longueurBateau, array &$tableProbabilite): void
    {
        for ($estHorizontal = 0; $estHorizontal < 2; $estHorizontal++) {
            $this->siPlacablePlacer($row, $col,$longueurBateau, $tableProbabilite, $estHorizontal);
        }
    }

    private function siPlacablePlacer(int $row, int $col, int $longueurBateau, array &$tableProbabilite, bool $estHorizontal): void
    {
        if ($this->placable($row, $col, $longueurBateau, $estHorizontal, $tableProbabilite)) {
            $boost = $this->overlappedHit($row, $col, $longueurBateau, $estHorizontal, $tableProbabilite);
            for ($k = 0; $k < $longueurBateau; $k++) {
                if ($tableProbabilite[$estHorizontal ? $row : $row + $k][$estHorizontal ? $col + $k : $col] >= 0)
                    $tableProbabilite[$estHorizontal ? $row : $row + $k][$estHorizontal ? $col + $k : $col] += $boost;
            }
        }
    }

    private function placable(int $row, int $col, int $longueur, bool $estHorizontal, array $tableProbabilite): bool
    {
        for ($i = 0; $i < $longueur; $i++) {
            if ($estHorizontal && $col + $i >= 10)
                return false;
            if (!$estHorizontal && $row + $i >= 10)
                return false;
            if ($estHorizontal && $tableProbabilite[$row][$col + $i] === -1)
                return false;
            if (!$estHorizontal && $tableProbabilite[$row + $i][$col] === -1)
                return false;
        }
        return true;
    }

    private function overlappedHit(int $row, int $col, int $longueur, bool $estHorizontal, array $tableProbabilite): int
    {
        $boost = 1;
        for ($i = 0; $i < $longueur; $i++) {
            if ($estHorizontal && $tableProbabilite[$row][$col + $i] === -2 && $col + $i + 1 < 10 && $col + $i - 1 >= 0 && ($tableProbabilite[$row][$col + $i + 1] === -2 || $tableProbabilite[$row][$col + $i - 1] === -2))
                $boost += 20;
            if (!$estHorizontal && $tableProbabilite[$row + $i][$col] === -2 && $row + $i + 1 < 10 && $row + $i - 1 >= 0 && ($tableProbabilite[$row + $i + 1][$col] === -2 || $tableProbabilite[$row + $i - 1][$col] === -2 ))
                $boost += 20;
            if ($estHorizontal && $tableProbabilite[$row][$col + $i] === -2)
                $boost += 10;
            if (!$estHorizontal && $tableProbabilite[$row + $i][$col] === -2)
                $boost += 10;
        }
        return $boost;
    }

    private function trouverPlusProbable(array $tableProbabilite, int $taille_tableau): string
    {
        $plusProbable = 0;
        $plusProbables = [];
        for ($i = 0; $i < $taille_tableau; $i++) {
            for ($j = 0; $j < $taille_tableau; $j++) {
                if ($tableProbabilite[$i][$j] == $plusProbable) {
                    $plusProbable = $tableProbabilite[$i][$j];
                    $plusProbables [] = chr(65 + $i) . "-" . ($j + 1);
                }
                else if ($tableProbabilite[$i][$j] > $plusProbable) {
                    $plusProbables = [];
                    $plusProbable = $tableProbabilite[$i][$j];
                    $plusProbables [] = chr(65 + $i) . "-" . ($j + 1);
                }
            }
        }
        return $plusProbables[rand(0, count($plusProbables) - 1)];
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
                    $tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($tableProbabilite, $row, $col, 5);
                    break;
                case 3:
                    unset($bateaux['cuirasse']);
                    $tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($tableProbabilite, $row, $col, 4);
                    break;
                case 4:
                    unset($bateaux['destroyer']);
                    $tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($tableProbabilite, $row, $col, 3);
                    break;
                case 5:
                    unset($bateaux['sous-marin']);
                    $tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($tableProbabilite, $row, $col, 3);
                    break;
                case 6:
                    unset($bateaux['patrouilleur']);
                    $tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($tableProbabilite, $row, $col, 2);
                    break;
                case -1:
                    break;
            }
        }
    }

    private function convertCoordonneesToIndex(string $coordonnee): array
    {
        $lettre = substr($coordonnee, 0, 1);
        $row = ord($lettre) - ord('A');
        $col = intval(substr($coordonnee, 2)) - 1;
        return ['row' => $row, 'col' => $col];
    }

    private function trouverPositionsBateauxEtMettreMiss(array &$tableProbabilite, int $row, int $col, int $longueur): void {
        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

        foreach ($directions as $direction) {
            $dirRow = $direction[0];
            $dirCol = $direction[1];
            $positionsTrouvees = true;

            for ($i = 0; $i < $longueur; $i++) {
                $checkRow = $row + $dirRow * $i;
                $checkCol = $col + $dirCol * $i;

                if (!$this->validerPosition($checkRow, $checkCol) || $tableProbabilite[$checkRow][$checkCol] !== -2) {
                    $positionsTrouvees = false;
                    break;
                }
            }

            if ($positionsTrouvees) {
                for ($i = 0; $i < $longueur; $i++) {
                    $tableProbabilite[$row + $dirRow * $i][$col + $dirCol * $i] = -1;
                }
            }
        }
    }

    private function validerPosition(int $row, int $col): bool {
        return $row >= 0 && $row < 10 && $col >= 0 && $col < 10;
    }
}
