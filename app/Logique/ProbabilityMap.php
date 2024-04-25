<?php

namespace App\Logique;

use App\Models\Missile;
use App\Models\Partie;

class ProbabilityMap
{
    private Partie $partie;
    private int $taille_tableau;
    private array $bateaux;
    private array $tableProbabilite;

    public function __construct(Partie $partie) {
        $this->taille_tableau = 10;
        $this->partie = $partie;
        $this->bateaux = [
            'porte-avions' => 5,
            'cuirasse' => 4,
            'destroyer' => 3,
            'sous-marin' => 3,
            'patrouilleur' => 2
        ];
        $this->tableProbabilite = array_fill(0, $this->taille_tableau, array_fill(0, $this->taille_tableau, 0));
    }

    public function calculateProbabilityMap(): string
    {

        $this->updateTableProbabiliteEtBateaux();

        $this->calculateBateauxConfigurations();

        return $this->trouverPlusProbable();
    }

    private function calculateBateauxConfigurations(): void
    {
        foreach ($this->bateaux as $bateau => $longueur) {
            $this->passerDansLesCase($longueur);
        }
    }

    private function passerDansLesCase(int $longueurBateau): void {
        for ($i = 0; $i < $this->taille_tableau; $i++) {
            for ($j = 0; $j < $this->taille_tableau; $j++) {
                $this->changerOrientationBateau($i, $j, $longueurBateau);
            }
        }
    }

    private function changerOrientationBateau(int $row, int $col, int $longueurBateau): void
    {
        for ($estHorizontal = 0; $estHorizontal < 2; $estHorizontal++) {
            $this->siPlacablePlacer($row, $col, $longueurBateau, $estHorizontal);
        }
    }

    private function siPlacablePlacer(int $row, int $col, int $longueurBateau, bool $estHorizontal): void
    {
        if ($this->placable($row, $col, $longueurBateau, $estHorizontal)) {
            $boost = $this->overlappedHit($row, $col, $longueurBateau, $estHorizontal);
            for ($k = 0; $k < $longueurBateau; $k++) {
                if ($this->tableProbabilite[$estHorizontal ? $row : $row + $k][$estHorizontal ? $col + $k : $col] >= 0)
                    $this->tableProbabilite[$estHorizontal ? $row : $row + $k][$estHorizontal ? $col + $k : $col] += $boost;
            }
        }
    }

    private function placable(int $row, int $col, int $longueur, bool $estHorizontal): bool
    {
        for ($i = 0; $i < $longueur; $i++) {
            if ($estHorizontal && $col + $i >= 10)
                return false;
            if (!$estHorizontal && $row + $i >= 10)
                return false;
            if ($estHorizontal && $this->tableProbabilite[$row][$col + $i] === -1)
                return false;
            if (!$estHorizontal && $this->tableProbabilite[$row + $i][$col] === -1)
                return false;
        }
        return true;
    }

    private function overlappedHit(int $row, int $col, int $longueur, bool $estHorizontal): int
    {
        $boost = 1;
        for ($i = 0; $i < $longueur; $i++) {
            if ($estHorizontal && $this->tableProbabilite[$row][$col + $i] === -2 && $col + $i + 1 < 10 && $col + $i - 1 >= 0 &&
                ($this->tableProbabilite[$row][$col + $i + 1] === -2 || $this->tableProbabilite[$row][$col + $i - 1] === -2))
                $boost += 20;
            if (!$estHorizontal && $this->tableProbabilite[$row + $i][$col] === -2 && $row + $i + 1 < 10 && $row + $i - 1 >= 0 &&
                ($this->tableProbabilite[$row + $i + 1][$col] === -2 || $this->tableProbabilite[$row + $i - 1][$col] === -2 ))
                $boost += 20;
            if ($estHorizontal && $this->tableProbabilite[$row][$col + $i] === -2)
                $boost += 10;
            if (!$estHorizontal && $this->tableProbabilite[$row + $i][$col] === -2)
                $boost += 10;
        }
        return $boost;
    }

    private function trouverPlusProbable(): string
    {
        $plusProbable = 0;
        $plusProbables = [];
        for ($i = 0; $i < $this->taille_tableau; $i++) {
            for ($j = 0; $j < $this->taille_tableau; $j++) {
                if ($this->tableProbabilite[$i][$j] == $plusProbable) {
                    $plusProbable = $this->tableProbabilite[$i][$j];
                    $plusProbables [] = chr(65 + $i) . "-" . ($j + 1);
                }
                else if ($this->tableProbabilite[$i][$j] > $plusProbable) {
                    $plusProbables = [];
                    $plusProbable = $this->tableProbabilite[$i][$j];
                    $plusProbables [] = chr(65 + $i) . "-" . ($j + 1);
                }
            }
        }
        return $plusProbables[rand(0, count($plusProbables) - 1)];
    }

    private function updateTableProbabiliteEtBateaux(): void
    {
        $missilesLancer = $this->partie->missiles()->get()->toArray();

        for ($i = 0; $i < count($missilesLancer); $i++) {
            $resultat = $missilesLancer[$i]['resultat'];
            $indexes = $this->convertCoordonneesToIndex($missilesLancer[$i]['coordonnee']);
            $row = $indexes['row'];
            $col = $indexes['col'];
            $resultat = $resultat === null ? -1 : $resultat;

            switch ($resultat) {
                case 0:
                    $this->tableProbabilite[$row][$col] = -1;
                    break;
                case 1:
                    $this->tableProbabilite[$row][$col] = -2;
                    break;
                case 2:
                    unset($this->bateaux['porte-avions']);
                    $this->tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($row, $col, 5);
                    break;
                case 3:
                    unset($this->bateaux['cuirasse']);
                    $this->tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($row, $col, 4);
                    break;
                case 4:
                    unset($this->bateaux['destroyer']);
                    $this->tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($row, $col, 3);
                    break;
                case 5:
                    unset($this->bateaux['sous-marin']);
                    $this->tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($row, $col, 3);
                    break;
                case 6:
                    unset($this->bateaux['patrouilleur']);
                    $this->tableProbabilite[$row][$col] = -2;
                    $this->trouverPositionsBateauxEtMettreMiss($row, $col, 2);
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

    private function trouverPositionsBateauxEtMettreMiss(int $row, int $col, int $longueur): void {
        $directions = [[-1, 0], [1, 0], [0, -1], [0, 1]];

        foreach ($directions as $direction) {
            $dirRow = $direction[0];
            $dirCol = $direction[1];
            $positionsTrouvees = true;

            for ($i = 0; $i < $longueur; $i++) {
                $checkRow = $row + $dirRow * $i;
                $checkCol = $col + $dirCol * $i;

                if (!$this->validerPosition($checkRow, $checkCol) || $this->tableProbabilite[$checkRow][$checkCol] !== -2) {
                    $positionsTrouvees = false;
                    break;
                }
            }

            if ($positionsTrouvees) {
                for ($i = 0; $i < $longueur; $i++) {
                    $this->tableProbabilite[$row + $dirRow * $i][$col + $dirCol * $i] = -1;
                }
            }
        }
    }

    private function validerPosition(int $row, int $col): bool {
        return $row >= 0 && $row < 10 && $col >= 0 && $col < 10;
    }
}
