<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartieRequest;
use App\Http\Resources\PartieCollection;
use App\Http\Resources\PartieResource;
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
        $partie = Partie::create($request->validated());
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
        $partie->delete();
        return new PartieResource($partie);
    }
}
