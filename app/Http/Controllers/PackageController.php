<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class PackageController extends Controller
{
    public function index()
    {
        $Package = Package::all();
        return response()->json($Package);
    }

    // to display in the add pet membership
    public function getUserPackages()
{
    Log::info('Fetching user packages wher status user is true');
    
    $packages = Package::where('status_user', 1)->get();
    Log::info($packages);
    return response()->json($packages);
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

    // Add the delete method:
    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return response()->json(['message' => 'Package deleted successfully']);
    }

    // Create a new package
    public function store(Request $request)
    {
        // Validate incoming data; adjust rules as needed
        $validated = $request->validate([
            'title'         => 'required|string|max:64',
            'description'   => 'required|string|max:256',
            'price'         => 'required|numeric',
            'duration'      => 'required|string|max:64',
            'second_price'  => 'required|numeric',
            'is_free_trial' => 'required|boolean',
            'status_user'   => 'required|boolean',
            'status_staff'  => 'required|boolean',
            'start_date'    => 'nullable|date', // ✅ Ensures valid date format
            'end_date'      => 'nullable|date|after_or_equal:start_date', // ✅ Ensures valid date and is after start_date
        ]);

        $package = Package::create($validated);

        return response()->json([
            'message' => 'Package created successfully',
            'package' => $package
        ], 201);
    }

    public function update(Request $request, $id)
{
    // Validate the incoming data. Adjust the rules as needed.
    $validated = $request->validate([
        'title' => 'required|string|max:64',
         'description' => 'required|string|max:256',
         'price' => 'required|numeric',
         'duration' => 'required|string|max:64',
         'second_price' => 'required|numeric',
         'is_free_trial' => 'required|boolean',
        'status_user' => 'sometimes|required|boolean',
        'status_staff' => 'sometimes|required|boolean',
        'start_date' => 'nullable|date',
         'end_date' => 'nullable|date|after_or_equal:start_date',
         'default_delivery' => 'nullable|string',
         'apply_first_offer' => 'nullable|string',
         'apply_second_offer' => 'nullable|string',
         'title_ar' => 'nullable|string',
         'description_ar' => 'nullable|string'
    ]);
    

    // Find the package and update it
    $package = Package::findOrFail($id);
    $package->update($validated);

    return response()->json([
        'message' => 'Package updated successfully',
        'package' => $package
    ], 200);
}

}
