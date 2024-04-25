<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartieRequest;
use App\Http\Resources\PartieCollection;
use App\Http\Resources\PartieResource;
use App\Logique\PlaceurBateaux;
use App\Models\Bateau;
use App\Models\Partie;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PartieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * TODO: pomal sur faut l'enlever
     */
    public function index(): PartieCollection
    {
        return new PartieCollection(Partie::all());
    }

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
        $setBateaux = new Bateau();
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
     * Display the specified resource.
     *
     * TODO: avoir si on garde
     */
    public function show(Partie $partie): PartieResource
    {
        return new PartieResource($partie);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partie $partie): PartieResource
    {
        Gate::denyIf($partie->user_id !== Auth::id(), 'Cette action n’est pas autorisée.');

        $bateaux = $partie->bateaux;
        $partie->delete();
        $partie->bateaux = $bateaux;
        return new PartieResource($partie);
    }
}
