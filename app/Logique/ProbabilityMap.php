<?php

namespace App\Logique;

use App\Models\Partie;

/**
 * Contient les informations et l'algorithme de recherche de la Map de probabilité.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class ProbabilityMap
{
    private Partie $partie;
    private int $taille_tableau;
    private array $bateaux;
    private array $tableProbabilite;

    /**
     * Constructeur.
     *
     * @param Partie $partie La partie.
     */
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

    /**
     * Fonction qui fait la map de probabilité et qui calcule le plus probable.
     *
     * @return string Les coordonnées les plus probables.
     */
    public function calculateProbabilityMap(): string
    {

        $this->updateTableProbabiliteEtBateaux();

        $this->calculateBateauxConfigurations();

        return $this->trouverPlusProbable();
    }

    /**
     * Fonction qui update la map avec les hits, les miss et les bateaux couler.
     *
     * @return void
     */
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

    /**
     * Fonction qui trouve les positions des bateaux coulés et remplace les hits par des miss.
     *
     * @param int $row La rangée de la coordonnée.
     * @param int $col La colonnes de la coordonnée.
     * @param int $longueur La longueur du bateau.
     * @return void
     */
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

    /**
     * Fonction qui valide si les positions sont dans la grid.
     *
     * @param int $row La rangée.
     * @param int $col La colonne.
     * @return bool Si la coordonnée est valide.
     */
    private function validerPosition(int $row, int $col): bool {
        return $row >= 0 && $row < 10 && $col >= 0 && $col < 10;
    }

    /**
     * Fonction qui ajoute toutes les configurations de l'array bateaux.
     *
     * @return void
     */
    private function calculateBateauxConfigurations(): void
    {
        foreach ($this->bateaux as $bateau => $longueur) {
            $this->passerDansLesCase($longueur);
        }
    }

    /**
     * Fonction qui prend un bateau et le fait passser dans chaque case de la grid.
     *
     * @param int $longueurBateau La longueur du bateau.
     * @return void
     */
    private function passerDansLesCase(int $longueurBateau): void {
        for ($i = 0; $i < $this->taille_tableau; $i++) {
            for ($j = 0; $j < $this->taille_tableau; $j++) {
                $this->changerOrientationBateau($i, $j, $longueurBateau);
            }
        }
    }

    /**
     * Fonction qui prend un bateau dans une case précise et qui calcule les probabilités des 2 orientations.
     *
     * @param int $row La rangée de la case.
     * @param int $col La colonne de la case.
     * @param int $longueurBateau La longueur du bateau.
     * @return void
     */
    private function changerOrientationBateau(int $row, int $col, int $longueurBateau): void
    {
        for ($estHorizontal = 0; $estHorizontal < 2; $estHorizontal++) {
            $this->siPlacablePlacer($row, $col, $longueurBateau, $estHorizontal);
        }
    }

    /**
     * Fonction qui vérifie si le bateau est placable à cet endroit et si oui on incrémente la valeur des cases où le
     * bateau pourrait être placer.
     *
     * @param int $row La rangée de la case du bateau.
     * @param int $col La colonne de la case du bateau.
     * @param int $longueurBateau La longueur du bateau.
     * @param bool $estHorizontal L'orientation du bateau.
     * @return void
     */
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

    /**
     * Fonction qui vérifie si le bateau est placable.
     *
     * @param int $row La rangée de la case du bateau.
     * @param int $col La colonne de la case du bateau.
     * @param int $longueur La longueur du bateau.
     * @param bool $estHorizontal L'orientation du bateau.
     * @return bool Si le bateau est placable à cet endroit avec cette orientation.
     */
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

    /**
     * Vérifie si le bateau overlap un hit si oui on incrémente le boost donner à la configuration du bateau,
     * si il en overlap 2 de suite on incrémente d'avantage.
     *
     * @param int $row La rangée de la case du bateau.
     * @param int $col La colonne de la case du bateau.
     * @param int $longueur La longueur du bateau.
     * @param bool $estHorizontal L'orientation du bateau.
     * @return int Le boost qu'on doit donner à cette configuration du bateau.
     */
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

    /**
     * Trouve les coordonnées de la case la plus probable. Si plusieurs cases sont également propable nous retournons
     * les coordonnées d'une case qui est des plus probables au hasard.
     *
     * @return string Les coordonnées de la case la plus probable.
     */
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

    /**
     * Fonction qui prend des coordonnées et retourne les index de la rangée et de la colonne.
     *
     * @param string $coordonnee Les coordonnées du missile.
     * @return array
     */
    private function convertCoordonneesToIndex(string $coordonnee): array
    {
        $lettre = substr($coordonnee, 0, 1);
        $row = ord($lettre) - ord('A');
        $col = intval(substr($coordonnee, 2)) - 1;
        return ['row' => $row, 'col' => $col];
    }
}
