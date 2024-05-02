<?php

namespace App\Http\Controllers;

use App\Http\Requests\MissileRequest;
use App\Http\Resources\MissileResource;
use App\Logique\ProbabilityMap;
use App\Models\Missile;
use App\Models\Partie;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Controller des méthodes des missiles.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard.
 */
class MissileController extends Controller
{
    /**
     * Méthode store d'un missile qui crée un missile.
     *
     * @param MissileRequest $request La requête.
     * @param Partie $partie La partie dans laquelle le missile est créer.
     * @return MissileResource Le missile créer.
     */
    public function store(MissileRequest $request, Partie $partie): MissileResource
    {
        Gate::denyIf($partie->user_id !== Auth::id(), "Cette action n’est pas autorisée.");

        $probabilityMap = new ProbabilityMap($partie);
        $coordonnee = $probabilityMap->calculateProbabilityMap();

        $missile = new Missile();
        $missile->coordonnee = $coordonnee;
        $missile->resultat = null;
        $missile->partie_id = $partie->id;
        $missile->save();

        return new MissileResource($missile);
    }

    /**
     * Méthode put d'un missile qui update le résultat d'un missile lancer.
     *
     * @param MissileRequest $request La requête.
     * @param Partie $partie La partie dans laquelle le missile à été créer.
     * @param string $coordonnee La coordonnée du missile à updater.
     * @return MissileResource Le missile updater.
     */
    public function update(MissileRequest $request, Partie $partie, string $coordonnee): MissileResource
    {
        $missile = Missile::where([
            ['coordonnee', $coordonnee],
            ['partie_id', $partie->id]])->firstOrFail();

        Gate::denyIf($partie->user_id !== Auth::id(), "Cette action n’est pas autorisée.");

        $request->validate([
            'resultat' => 'required'
        ]);

        $attibutes = $request->validated();

        $missile->resultat = $attibutes['resultat'];
        $missile->save();

        return new MissileResource($missile);
    }
}
