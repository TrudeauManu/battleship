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
     * Store a newly created resource in storage.
     */
    public function store(PartieRequest $request): PartieResource
    {
        $partie = new Partie();
        $partie->adversaire = $request->validated()['adversaire'];
        $partie->user_id = auth()->user()->id;
        $partie->save();

        $placeurBateaux = new PlaceurBateaux();

        $bateaux = $placeurBateaux->placerBateaux();
//        $estCoter = rand(0, 1) >= 0.8;
//        if ($estCoter) {
//            $bateaux = $placeurBateaux->placerBateauxCote();
//        } else {
//            $bateaux = $placeurBateaux->placerBateaux();
//        }

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
     * Remove the specified resource from storage.
     */
    public function destroy(Partie $partie): PartieResource
    {
        Gate::denyIf($partie->user_id !== Auth::id(), "Cette action n’est pas autorisée.");

        $bateaux = $partie->bateaux;
        $partie->delete();
        $partie->bateaux = $bateaux;
        return new PartieResource($partie);
    }
}
