<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $Package = Package::all();
        return response()->json($Package);
    }

    public function show($id)
    {
        $Package = Package::findOrFail($id);
        return response()->json($Package);
    }

    // public function getProductsByProvider($provider_id)
    // {
    //     $products = Package::where('provider_id', $provider_id)->get();
    //     if ($products->isEmpty()) {
    //         return response()->json(['message' => 'No products found'], 404);
    //     }
    //     return response()->json(['data' => $products], 200);
    // }
}
