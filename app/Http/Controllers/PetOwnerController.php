<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Support\Facades\Log;
use App\Models\PetOwner;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Http\Resources\PetOwnerResource;
use App\Models\Membership;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PetOwnerController extends Controller
{
    /**
     * Retrieve and display a list of all pet owners.
     */
    public function index()
    {
        $petOwners = PetOwner::all();
        return PetOwnerResource::collection($petOwners);
    }

    // private function userStatus($id)
    // {
    //     $petOwner = PetOwner::findOrFail($id);
    //     $pets = $petOwner->pets;
    //     $isM = false;
    //     $isFreeTrial = false;
    //     for ($i = 0; $i < count($pets); $i++) {
    //         $membership = Membership::where('pet_id', $pets[$i]->id)->first();
    //         if ($membership) {
    //             $isM = true;
    //             $package = $membership->package;
    //             $package->is_free_trial ? $isFreeTrial = true : $isFreeTrial = false;
    //             if ($isFreeTrial) {
    //                 return 'Free trial';
    //             }
    //         }
    //     }
    //     if ($isM) {
    //         return 'Member';
    //     } else {
    //         return 'Non-member';
    //     }
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created pet owner in the database.
     */
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'first_name' => 'required|alpha|min:3|max:31',
    //         'last_name' => 'required|alpha|min:3|max:31',
    //         'email' => 'required|email:rfc,dns|unique:pet_owners',
    //         'password' => 'required|min:8',
    //         'nationality' => 'required|string',
    //         'profile_image' => 'required|string',
    //         'location' => 'required|string|max:32',
    //         'date_of_birth' => 'required|date',
    //         'house' => 'nullable|string|max:255',
    //         'road' => 'nullable|string|max:255',
    //         'block' => 'nullable|string|max:255',
    //         'building_name' => 'nullable|string|max:255',
    //         'apt_number' => 'nullable|string|max:255',
    //         'floor' => 'nullable|string|max:255',
    //         'company' => 'nullable|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $petOwner = PetOwner::create([
    //         'first_name' => $request->first_name,
    //         'last_name' => $request->last_name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'nationality' => $request->nationality,
    //         'profile_image' => $request->profile_image,
    //         'location' => $request->location,
    //         'date_of_birth' => $request->date_of_birth,
    //         'house' => $request->house,
    //         'road' => $request->road,
    //         'block' => $request->block,
    //         'building_name' => $request->building_name,
    //         'apt_number' => $request->apt_number,
    //         'floor' => $request->floor,
    //         'company' => $request->company,
    //     ]);

    //     return new PetOwnerResource($petOwner);
    // }


    /**
     * Display the specified pet owner.
     */

    public function show(PetOwner $pet_owner)
    {
        Log::info('PetOwner data=============>:', $pet_owner->toArray());
        return new PetOwnerResource($pet_owner);
    }

    public function getByEmail(Request $request)
    {
        $email = $request->email; // Retrieve the email from query parameters
        if (!$email) {
            return response()->json(['error' => 'Email is required'], 400);
        }

        $petOwner = PetOwner::where('email', $email)->first();

        if (!$petOwner) {
            return response()->json(['error' => 'No pet owner found with that email'], 404);
        }

        return response()->json(['ownerId' => $petOwner->id]);
    }

    public function view($id)
    {
        $petOwner = PetOwner::findOrFail($id);
        // $status = $this->userStatus($id);

        return response()->json(array_merge($petOwner->toArray(), ['status' => $petOwner->status]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * UUpdate the specified pet owner in the database.
     */
    public function update(Request $request, string $id)
    {
        $petOwner = PetOwner::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|alpha|min:3|max:31',
            'last_name' => 'required|alpha|min:3|max:31',
            'email' => 'sometimes|required|email:rfc,dns|unique:pet_owners,email,' . $petOwner->id,
            'password' => 'nullable|min:8',
            'nationality' => 'required|string',
            'phone' => 'required|string',
            'location' => 'required|string|max:32',
            'city' => 'required|string|max:32',
            'gender' => 'required|in:m,f',
            'date_of_birth' => 'required|date',
            'house' => 'nullable|string|max:255',
            'road' => 'nullable|string|max:255',
            'block' => 'nullable|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'apt_number' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $petOwner->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => $request->password ? Hash::make($request->password) : $petOwner->password,
            'nationality' => $request->nationality,
            'phone' => $request->phone,
            'location' => $request->location,
            'city' => $request->city,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'house' => $request->house,
            'road' => $request->road,
            'block' => $request->block,
            'building_name' => $request->building_name,
            'apt_number' => $request->apt_number,
            'floor' => $request->floor,
            'company' => $request->company,
        ]);

        return new PetOwnerResource($petOwner);
    }

    public function update_profile_image(Request $request, $id)
    {
        $petOwner = PetOwner::findOrFail($id);

        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase service account file does not exist at path: ' . $serviceAccountPath], 500);
        }

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket();

        $profileImage = $request->file('profile_image');
        $fileName = $profileImage->getClientOriginalName();
        $filePath = 'profile_images/' . uniqid() . '_' . $fileName;

        $firebaseFile = fopen($profileImage->getPathname(), 'r');
        $bucket->upload($firebaseFile, [
            'name' => $filePath,
            'predefinedAcl' => 'publicRead'
        ]);

        $imageUrl = "https://storage.googleapis.com/{$bucket->name()}/{$filePath}";

        $petOwner->update([
            'profile_image' => $imageUrl,

        ]);

        return new PetOwnerResource($petOwner);
    }

    public function updatePass(Request $request)
    {
        // Validate the email and password fields
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the pet owner by email
        $petOwner = PetOwner::where('email', $request->email)->first();

        // If no pet owner is found, return an error
        if (!$petOwner) {
            return response()->json(['message' => 'Pet owner not found.'], 404);
        }

        // Update the password
        $petOwner->update([
            'password' => Hash::make($request->password),
        ]);

        // Return success response
        return response()->json(['message' => 'Password updated successfully.']);
    }


    /**
     * Remove the specified pet owner from database.
     */
    public function destroy(string $id)
    {
        $petOwner = PetOwner::findOrFail($id);
        $petOwner->delete();
        return response()->json(['message' => 'Pet owner deleted successfully'], 200);
    }
}
