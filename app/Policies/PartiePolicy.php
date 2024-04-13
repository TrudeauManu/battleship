<?php

namespace App\Policies;

use App\Models\Missile;
use App\Models\Partie;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PartiePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function tirer(Partie $partie): Response
    {
        if (!$partie->est_tour) {
            return Response::deny('Cette action n’est pas autorisée.');
        }

        return Response::allow();
    }

    public function repondre(User $user, Partie $partie, Missile $missile)
    {
        return is_null($missile->resultat) && !$partie->estSonTour($user);
    }
}
