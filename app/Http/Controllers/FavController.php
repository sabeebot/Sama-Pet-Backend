<?php

namespace App\Http\Controllers;

use App\Models\Fav;
use Illuminate\Http\Request;

class FavController extends Controller
{
    public function index()
    {
        return Fav::all();
    }

    public function show($id)
    {
        return Fav::findOrFail($id);
    }

    public function store(Request $request)
    {
        $fav = Fav::create($request->all());
        return response()->json($fav, 201);
    }

    public function update(Request $request, $id)
    {
        $fav = Fav::findOrFail($id);
        $fav->update($request->all());
        return response()->json($fav, 200);
    }

    public function destroy($id)
    {
        Fav::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function getFavsByPetOwnerId($id)
    {
        $favs = Fav::where('pet_owner_id', $id)->get();
        return response()->json($favs);
    }
}
