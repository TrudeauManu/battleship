<?php

namespace App\Policies;

use App\Models\Partie;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Contient les règles de policies de la partie.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard
 */
class PartiePolicy
{
    /**
     * Détermine si l'utilisateur peut créer la partie.
     */
    public function create(User $user, Partie $partie): bool
    {
        return $user->id === $partie->user_id;
    }

    /**
     * Détermine si l'utilisateur peut modifier la partie.
     */
    public function update(User $user, Partie $partie): bool
    {
        return $user->id === $partie->user_id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer la partie.
     */
    public function delete(User $user, Partie $partie): bool
    {
        return $user->id === $partie->user_id;
    }
}
