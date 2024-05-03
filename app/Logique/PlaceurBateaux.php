<?php

namespace App\Logique;

/**
 * Classe de placeur de bateaux.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class PlaceurBateaux
{
    private int $taille_tableau;
    private array $bateaux_longueur;
    private array $tableau;
    private array $bateaux;

    /**
     * Constructeur.
     */
    public function __construct()
    {
        $this->taille_tableau = 10;
        $this->bateaux_longueur = [
            'porte-avions' => 5,
            'cuirasse' => 4,
            'destroyer' => 3,
            'sous-marin' => 3,
            'patrouilleur' => 2
        ];

        $this->tableau = array_fill(0, $this->taille_tableau, array_fill(0, $this->taille_tableau, 0));

        $this->bateaux = [
            'porte-avions' => [],
            'cuirasse' => [],
            'destroyer' => [],
            'sous-marin' => [],
            'patrouilleur' => []
        ];
    }

    /**
     * Fonction qui place les bateaux à des positions au hasard.
     *
     * @return array[] Les positions des bateaux.
     */
    public function placerBateaux(): array {
        foreach ($this->bateaux_longueur as $bateau => $longueur) {
            $placer = false;
            $estHorizontal = rand(0,1);
            while (!$placer) {
                if ($estHorizontal) {
                    $row = rand(0, $this->taille_tableau - 1);
                    $col = rand(0, $this->taille_tableau - $longueur);
                } else {
                    $row = rand(0, $this->taille_tableau - $longueur);
                    $col = rand(0, $this->taille_tableau - 1);
                }

                $placer = !$this->overlappedOrNextTo($row, $col, $longueur, $estHorizontal);
                if ($placer) {
                    $this->placer($row, $col, $longueur, $estHorizontal, $bateau);
                }
            }
        }

        return $this->bateaux;
    }

    /**
     * Fonction qui vérifie que le bateau n'overlap pas d'autre bateau et qu'il n'est pas directement à côter d'un
     * autre bateau déja placer.
     *
     * @param int $row La rangée de la case du bateau.
     * @param int $col La colonne de la case du bateau.
     * @param int $longueur La longueur du bateau.
     * @param bool $estHorizontal L'orientation du bateau.
     * @return bool Si le bateau est placable.
     */
    private function overlappedOrNextTo(int $row, int $col, int $longueur, bool $estHorizontal): bool {
        for ($i = 0; $i < $longueur; $i++) {

            if ($estHorizontal) {
                if($this->tableau[$row][$col + $i] === 1 ||
                    ($row > 0 && $this->tableau[$row - 1][$col + $i] === 1) ||
                    ($row < count($this->tableau) - 1 && $this->tableau[$row + 1][$col + $i] === 1) ||
                    ($col + $i > 0 && $this->tableau[$row][$col + $i - 1] === 1) ||
                    ($col + $i < count($this->tableau[0]) - 1 && $this->tableau[$row][$col + $i + 1] === 1)) {
                    return true;
                }
                if ($longueur <= 3 && (($col + $i >= 3 && $col + $i <= 6) && ($row >= 3 && $row <= 6)))
                    return true;
            } else {
                if ($this->tableau[$row + $i][$col] === 1 ||
                    ($col > 0 && $this->tableau[$row + $i][$col - 1] === 1) ||
                    ($col < count($this->tableau[0]) - 1 && $this->tableau[$row + $i][$col + 1] === 1) ||
                    ($row + $i > 0 && $this->tableau[$row + $i - 1][$col] === 1) ||
                    ($row + $i < count($this->tableau) - 1 && $this->tableau[$row + $i + 1][$col] === 1)) {
                    return true;
                }
                if ($longueur === 2 && ($row + $i >= 3 && $row + $i <= 6) && ($col >= 3 && $col <= 6))
                    return true;
            }
        }
        return false;
    }

    /**
     * Fonction qui place le bateau et ajoute ses coordonnées dans l'array bateaux.
     *
     * @param int $row La rangée de la case du bateau.
     * @param int $col La colonne de la case du bateau.
     * @param int $longueur La longueur du bateau.
     * @param bool $estHorizontal L'orientation du bateau.
     * @param string $bateau Le nom du bateau.
     * @return void
     */
    private function placer(int $row, int $col, int $longueur, bool $estHorizontal, string $bateau): void {
        for ($i = 0; $i < $longueur; $i++) {
            $this->tableau[$estHorizontal ? $row : $row + $i][$estHorizontal ? $col + $i : $col] = 1;
            $this->bateaux[$bateau][] = chr(65 + ($estHorizontal ? $row : $row + $i)) . "-" . ($col + 1 + ($estHorizontal ? $i : 0));
        }
    }

    /**
     * Fonction qui place les bateaux en s'assurant qu'ils touchent au minimum un côté.
     *
     * @return array[] Les positions des bateaux.
     */
    public function placerBateauxCote(): array {
        foreach ($this->bateaux_longueur as $bateau => $longueur) {
            $placer = false;
            while (!$placer) {
                $estHorizontal = rand(0,1);
                if ($estHorizontal) {
                    $row = (rand(0, 1) <= 0.5 ? 0 : $this->taille_tableau - 1);
                    $col = rand(0, $this->taille_tableau - $longueur);
                } else {
                    $row = rand(0, $this->taille_tableau - $longueur);
                    $col = (rand(0, 1) <= 0.5 ? 0 : $this->taille_tableau - 1);
                }

                $placer = !$this->overlappedOrNextTo($row, $col, $longueur, $estHorizontal);
                if ($placer) {
                    $this->placer($row, $col, $longueur, $estHorizontal, $bateau);
                }
            }
        }

        return $this->bateaux;
    }
}
