<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberShipResource;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Package;
use App\Models\Pet;

class MemeberShipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Membership = Membership::all();
        return MemberShipResource::collection($Membership);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'package_id' => 'required|exists:packages,id',
                'pet_ids' => 'required|array',
                'pet_ids.*' => 'exists:pets,id',
            ]);

            // Retrieve the package
            $package = Package::findOrFail($request->package_id);

            // Calculate start and end dates
            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addDays($this->getDurationInDays($package->duration));

            // Iterate over the selected pets and create memberships
            foreach ($request->pet_ids as $petId) {
                // Ensure the pet exists
                $pet = Pet::findOrFail($petId);

                // Create the membership
                Membership::create([
                    'package_id' => $package->id,
                    'pet_id' => $pet->id,
                    'price' => $package->price,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }

            return response()->json(['message' => 'Membership(s) created successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle other errors
            return response()->json(['message' => 'An error occurred while creating the membership.'], 500);
        }
    }


    /**
     * Helper function to convert the package duration to days.
     */
    private function getDurationInDays($duration)
    {
        $durations = [
            '1 month' => 30,
            '3 months' => 90,
            '6 months' => 180,
            '1 year' => 365,
        ];

        return $durations[$duration] ?? 30;
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $membership = Membership::where('pet_id', $id)->firstOrFail();
        return new MemberShipResource($membership);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Validate that the ID exists
        $membership = Membership::find($id);
    
        if ($membership) {
            $membership->delete();
            return response()->json(['message' => 'Membership deleted successfully.'], 200);
        }
    
        return response()->json(['message' => 'Membership not found.'], 404);
    }
}
