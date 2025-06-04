<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberShipResource;
use App\Models\Membership;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Package;
use App\Models\Pet;
use Illuminate\Support\Facades\Log;


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



    public function getMembershipDetails($id)
{
    $membership = Membership::with('pet.owner')->find($id);

    if (!$membership) {
        return response()->json(['message' => 'Membership not found'], 404);
    }

    return response()->json([
        'pet' => $membership->pet,
        'owner' => $membership->pet->owner,
        'policy' => $membership
    ]);
}


    public function storeSingle(Request $request)
{
    try {
        // Validate the request data for a single membership, including new fields.
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'pet_id'     => 'required|exists:pets,id',
            'price'      => 'required|numeric',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'status'     => 'nullable|string|max:50',
            'pay_type'   => 'nullable|string|max:50',
            'delivery'   => 'nullable|string|max:50',
        ]);

        // Create membership record
        $membership = Membership::create([
            'package_id' => $request->package_id,
            'pet_id'     => $request->pet_id,
            'price'      => $request->price,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => $request->status,
            'pay_type'   => $request->pay_type,
            'delivery'   => $request->delivery,
        ]);

        // Create an invoice for this membership
        $invoice = Invoice::create([
            'membership_id' => $membership->id,
            // 'order_id' => null, // if you're not using an order, keep this null or remove it
        ]);

        return response()->json([
            'message' => 'Membership and invoice created successfully.',
            'membership' => $membership,
            'invoice' => $invoice
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['message' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Membership store error: ' . $e->getMessage());
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


public function update(Request $request, string $id)
{
    try {
        // Validate the incoming request data, including the new fields.
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'pet_id'     => 'required|exists:pets,id',
            'price'      => 'required|numeric',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'status'     => 'nullable|string|max:50',
            'pay_type'   => 'nullable|string|max:50',
            'delivery'   => 'nullable|string|max:50',
        ]);

        // Find the membership record by its ID
        $membership = Membership::findOrFail($id);

        // Update the membership with the new data
        $membership->update([
            'package_id' => $request->package_id,
            'pet_id'     => $request->pet_id,
            'price'      => $request->price,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => $request->status,
            'pay_type'   => $request->pay_type,
            'delivery'   => $request->delivery,
        ]);

        return response()->json(['message' => 'Membership updated successfully.']);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['message' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Membership update error: ' . $e->getMessage());
        return response()->json(['message' => $e->getMessage()], 500);
    }
}




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
