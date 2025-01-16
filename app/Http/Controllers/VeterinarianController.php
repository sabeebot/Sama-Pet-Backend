<?php

namespace App\Http\Controllers;

use App\Models\Veterinarian;
use Illuminate\Http\Request;

class VeterinarianController extends Controller
{
    public function index()
    {
        $veterinarians = Veterinarian::all();
        return response()->json($veterinarians);
    }

    public function show($id)
    {
        $veterinarian = Veterinarian::findOrFail($id);
        return response()->json($veterinarian);
    }
    
    public function getVersByProviderId($provider_id)
    {
        $veterinarians = Veterinarian::where('provider_id', $provider_id)->get();
        return response()->json($veterinarians);
    }
}
