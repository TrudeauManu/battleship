<?php

namespace App\Http\Controllers;

use App\Http\Requests\MissileRequest;
use App\Http\Requests\PartieRequest;
use App\Http\Resources\MissileResource;
use App\Http\Resources\PartieCollection;
use App\Http\Resources\PartieResource;
use App\Models\Bateau;
use App\Models\Coordonnee;
use App\Models\Missile;
use App\Models\Partie;
use Illuminate\Http\Request;

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
        $bateaux = [
            'porte-avions' => ["A-1", "A-2", "A-3", "A-4", "A-5"],
            'cuirasse' => ["B-1", "B-2", "B-3", "B-4"],
            'destroyer' => ["C-1", "C-2", "C-3"],
            'sous-marin' => ["D-1", "D-2", "D-3"],
            'patrouilleur' => ["E-1", "E-2"]
        ];

        $partie = Partie::create($request->validated());

        $setBateaux = new Bateau();
        $setBateaux->positions_porte_avions = json_encode($bateaux['porte-avions']);
        $setBateaux->positions_cuirasse = json_encode($bateaux['cuirasse']);
        $setBateaux->positions_destroyer = json_encode($bateaux['destroyer']);
        $setBateaux->positions_sous_marin = json_encode($bateaux['sous-marin']);
        $setBateaux->positions_patrouilleur = json_encode($bateaux['patrouilleur']);
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
     *
     * TODO: Soft delete si on implemente un apprentissage
     */
    public function destroy(Partie $partie): PartieResource
    {
        $partie->delete();
        return new PartieResource($partie);
    }

    public function shoot(MissileRequest $request, Partie $partie): MissileResource
    {
        $missile = (new Missile())->createMissile($partie);
        return new MissileResource($missile);
    }

    public function updateMissile(MissileRequest $request, Partie $partie, string $coordonnee): MissileResource
    {
        $missile = Missile::where('coordonnee', $coordonnee)->firstOrFail();

        $missile->resultat = $request->resultat;
        $missile->save();

        return new MissileResource($missile);
    }
}
