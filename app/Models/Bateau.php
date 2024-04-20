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

    public function placerBateaux(): array
    {
        $TAILLE_TABLEAU = 10;
        $BATEAUX_LONGUEUR = [
            'porte-avions' => 5,
            'cuirasse' => 4,
            'destroyer' => 3,
            'sous-marin' => 3,
            'patrouilleur' => 2
        ];

        $tableau = array_fill(0, $TAILLE_TABLEAU, array_fill(0, $TAILLE_TABLEAU, 0));

        $bateaux = [
            'porte-avions' => [],
            'cuirasse' => [],
            'destroyer' => [],
            'sous-marin' => [],
            'patrouilleur' => []
        ];

        foreach ($BATEAUX_LONGUEUR as $bateau => $longueur) {
            $placer = false;
            while (!$placer) {
                $estHorizontal = rand(0,1);
                if ($estHorizontal) {
                    $row = rand(0, $TAILLE_TABLEAU - 1);
                    $col = rand(0, $TAILLE_TABLEAU - $longueur);
                } else {
                    $row = rand(0, $TAILLE_TABLEAU - $longueur);
                    $col = rand(0, $TAILLE_TABLEAU - 1);
                }
                $placer = !$this->overlappedOrNextTo($tableau, $row, $col, $longueur, $estHorizontal);
                if ($placer) {
                    for ($i = 0; $i < $longueur; $i++) {
                        $tableau[$estHorizontal ? $row : $row + $i][$estHorizontal ? $col + $i : $col] = 1;
                        $bateaux[$bateau][] = chr(65 + ($estHorizontal ? $row : $row + $i)) . "-" . ($col + 1 + ($estHorizontal ? $i : 0));
                    }
                }
            }
        }

        return $bateaux;
    }

    function overlappedOrNextTo($tableau, $row, $col, $longueur, $estHorizontal): bool {
        for ($i = 0; $i < $longueur; $i++) {
            if ($estHorizontal) {
                if($tableau[$row][$col + $i] === 1 ||
                    ($row > 0 && $tableau[$row - 1][$col + $i] === 1) ||
                    ($row < count($tableau) - 1 && $tableau[$row + 1][$col + $i] === 1)) {
                    return true;
                }
            } else {
                if ($tableau[$row + $i][$col] === 1 ||
                    ($col > 0 && $tableau[$row + $i][$col - 1] === 1) ||
                    ($col < count($tableau[0]) - 1 && $tableau[$row + $i][$col + 1] === 1)) {
                    return true;
                }
            }
        }
        return false;
    }
}
