<?php

namespace App\Http\Controllers;

use App\Http\Requests\MissileRequest;
use App\Http\Resources\MissileResource;
use App\Models\Missile;
use App\Models\Partie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MissileController extends Controller
{
    public function shoot(MissileRequest $request, Partie $partie): MissileResource
    {
        Gate::denyIf($partie->user_id !== Auth::id(), 'Cette action n’est pas autorisée.');

        $missile = (new Missile())->createMissile($partie);

        return new MissileResource($missile);
    }

    public function updateMissile(MissileRequest $request, Partie $partie, string $coordonnee): MissileResource
    {
        $missile = Missile::where([
            ['coordonnee', $coordonnee],
            ['partie_id', $partie->id]])->firstOrFail();

        Gate::denyIf($partie->user_id !== Auth::id(), 'Cette action n’est pas autorisée.');

        $request->validate([
            'resultat' => 'required'
        ]);

        $attibutes = $request->validated();

        $missile->resultat = $attibutes['resultat'];
        $missile->save();

        return new MissileResource($missile);
    }
}
