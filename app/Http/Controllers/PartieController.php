<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartieRequest;
use App\Http\Resources\PartieResource;
use App\Logique\PlaceurBateaux;
use App\Models\Bateaux;
use App\Models\Partie;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Controller de partie.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class PartieController extends Controller
{
    /**
     * Méthode store qui crée une nouvelle partie.
     */
    public function store(PartieRequest $request): PartieResource
    {
        $partie = new Partie();
        $partie->adversaire = $request->validated()['adversaire'];
        $partie->user_id = auth()->user()->id;
        $partie->save();

        $placeurBateaux = new PlaceurBateaux();

        $bateaux = rand(0, 100) >= 66 ? $placeurBateaux->placerBateauxCote()
                                      : $placeurBateaux->placerBateaux();

        $setBateaux = new Bateaux();
        $setBateaux->positions_porte_avions = $bateaux['porte-avions'];
        $setBateaux->positions_cuirasse = $bateaux['cuirasse'];
        $setBateaux->positions_destroyer = $bateaux['destroyer'];
        $setBateaux->positions_sous_marin = $bateaux['sous-marin'];
        $setBateaux->positions_patrouilleur = $bateaux['patrouilleur'];
        $setBateaux->partie_id = $partie->id;
        $setBateaux->save();

        return new PartieResource($partie);
    }

    /**
     * Méthode destroy qui supprime la partie de la base de données.
     */
    public function destroy(Partie $partie): PartieResource
    {
        Gate::authorize('delete', $partie);

        $bateaux = $partie->bateaux;
        $partie->delete();
        $partie->bateaux = $bateaux;
        return new PartieResource($partie);
    }
}
