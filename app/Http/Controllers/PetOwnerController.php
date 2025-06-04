<?php

namespace App\Http\Controllers;

use App\Models\PetOwner;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Http\Resources\PetOwnerResource;
use App\Models\Membership;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PetOwnerController extends Controller
{
    /**
     * Display a listing of all pet owners.
     */
    public function index()
    {
        $petOwners = PetOwner::all();
        return PetOwnerResource::collection($petOwners);
    }

    /**
     * Store a newly created pet owner in the database,
     * including uploading the profile image to Firebase.
     */
    public function store(Request $request)
    {
        // Updated validator to include the phone field
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|alpha|min:3|max:31',
            'last_name'     => 'required|alpha|min:3|max:31',
            'email'         => 'required|email:rfc,dns|unique:pet_owners,email',
            'password'      => 'required|min:8',
            'status'        => 'required|string',  // <-- Added status here
            'nationality'   => 'required|string',
            'phone'         => 'required|string', // Added phone field
            'profile_image' => 'required|string',  // Expecting a Base64 image string
            'city'          => 'required|string|max:32',  // <-- Add this line
            'date_of_birth' => 'required|date',
            'gender'        => 'required|in:m,f', // Gender is now required
            'house'         => 'nullable|string|max:255',
            'road'          => 'nullable|string|max:255',
            'block'         => 'nullable|string|max:255',
            'building'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Process the profile image upload via Firebase
        $profileImageBase64 = $data['profile_image'];

        if (preg_match('/^data:image\/(\w+);base64,/', $profileImageBase64, $matches)) {
            $extension = strtolower($matches[1]); // e.g., jpg, png
            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                return response()->json(['error' => 'Invalid image type. Only JPG, JPEG, and PNG are allowed.'], 422);
            }
            $profileImageBase64 = substr($profileImageBase64, strpos($profileImageBase64, ',') + 1);
            $imageData = base64_decode($profileImageBase64);
            if ($imageData === false) {
                return response()->json(['error' => 'Base64 decoding failed.'], 422);
            }
        } else {
            return response()->json(['error' => 'Invalid image data.'], 422);
        }

        // Generate a unique filename and define the Firebase storage path
        $fileName = uniqid() . '.' . $extension;
        $firebasePath = 'profile_images/' . $fileName;

        // Ensure the Firebase service account file exists
        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase service account file not found.'], 500);
        }

        try {
            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();

            // Upload the image data to Firebase Storage with public-read access
            $bucket->upload($imageData, [
                'name'          => $firebasePath,
                'predefinedAcl' => 'publicRead'
            ]);

            // Construct the public URL for the uploaded image.
            $profileImageUrl = "https://storage.googleapis.com/{$bucket->name()}/{$firebasePath}";
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image upload failed: ' . $e->getMessage()], 500);
        }

        // Create the pet owner record in MySQL including the phone field
        $petOwner = PetOwner::create([
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'nationality'   => $data['nationality'],
            'phone'         => $data['phone'], // Added phone here
            'profile_image' => $profileImageUrl,
            'status'        => $data['status'],   // Save status here
            'date_of_birth' => $data['date_of_birth'],
            'house'         => $data['house'] ?? null,
            'road'          => $data['road'] ?? null,
            'gender'        => $data['gender'],
            'block'         => $data['block'] ?? null,
            'city'          => $data['city'],             // <-- Add this line
            'building'      => $data['building'] ?? null
,
        ]);

        return new PetOwnerResource($petOwner);
    }


    public function searchByEmail(Request $request)
{
    $email = $request->query('email');
    if (!$email) {
        return response()->json(['message' => 'Email is required'], 400);
    }
    
    $owner = PetOwner::where('email', $email)->first();
    if (!$owner) {
        return response()->json(['message' => 'Owner not found'], 404);
    }
    
    return response()->json($owner, 200);
}


    /**
     * Display the specified pet owner.
     */
    public function show($id)
{
    // Eager load pets and, for each pet, its membership
    $owner = PetOwner::with('pets.membership.package')->find($id); // two eager calling in one call we get pets to populate but we also get the membership to display amount of pet and amoutn of memebrship

    if (!$owner) {
        Log::error("Owner not found for ID: " . $id);
        return response()->json(['error' => 'Owner not found'], 404);
    }

    Log::info("Owner found: ", $owner->toArray());
    return new PetOwnerResource($owner);
}



    /**
     * Retrieve pet owner by email.
     */
    public function getByEmail(Request $request)
    {
        $email = $request->email;
        if (!$email) {
            return response()->json(['error' => 'Email is required'], 400);
        }

        $petOwner = PetOwner::where('email', $email)->first();

        if (!$petOwner) {
            return response()->json(['error' => 'No pet owner found with that email'], 404);
        }

        return response()->json(['ownerId' => $petOwner->id]);
    }

    /**
     * Update the specified pet owner.
     */
    public function update(Request $request, string $id)
    {
        $petOwner = PetOwner::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|alpha|min:3|max:31',
            'last_name'     => 'required|alpha|min:3|max:31',
            'email'         => 'sometimes|required|email:rfc,dns|unique:pet_owners,email,' . $petOwner->id,
            'password'      => 'nullable|min:8',
            'nationality'   => 'required|string',
            'phone'         => 'required|string',
            'status'        => 'required|string',  // <-- Added status here
            'city'          => 'required|string|max:32',
            'gender'        => 'required|in:m,f',
            'date_of_birth' => 'required|date',
            'house'         => 'nullable|string|max:255',
            'road'          => 'nullable|string|max:255',
            'block'         => 'nullable|string|max:255',
            'building'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set default to the existing image URL
    $profileImageUrl = $petOwner->profile_image;

    // Check if a new image is provided (it should be a base64 string starting with "data:image/")
    if (isset($request->profile_image) && preg_match('/^data:image\/(\w+);base64,/', $request->profile_image, $matches)) {
        Log::info('New image provided in update request.');
        $profileImageBase64 = $request->profile_image;
        $extension = strtolower($matches[1]);
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return response()->json(['error' => 'Invalid image type. Only JPG, JPEG, and PNG are allowed.'], 422);
        }
        $profileImageBase64 = substr($profileImageBase64, strpos($profileImageBase64, ',') + 1);
        $imageData = base64_decode($profileImageBase64);
        if ($imageData === false) {
            return response()->json(['error' => 'Base64 decoding failed.'], 422);
        }
        
        // Generate a unique filename and define the Firebase storage path
        $fileName = uniqid() . '.' . $extension;
        $firebasePath = 'profile_images/' . $fileName;
        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase service account file not found.'], 500);
        }

        try {
            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();

            // Upload the new image to Firebase
            $bucket->upload($imageData, [
                'name'          => $firebasePath,
                'predefinedAcl' => 'publicRead'
            ]);

            // Construct the public URL for the uploaded image.
            $profileImageUrl = "https://storage.googleapis.com/{$bucket->name()}/{$firebasePath}";
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image upload failed: ' . $e->getMessage()], 500);
        }
    }

        $petOwner->update([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'password'      => $request->password ? Hash::make($request->password) : $petOwner->password,
            'nationality'   => $request->nationality,
            'phone'         => $request->phone,
            'city'          => $request->city,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'house'         => $request->house,
            'status'        => $request->status, // Update status here
            'road'          => $request->road,
            'block'         => $request->block,
            'building'      => $request->building

        ]);

        return new PetOwnerResource($petOwner);
    }

    /**
     * Update the pet owner's profile image using file upload.
     */
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
            'name'          => $filePath,
            'predefinedAcl' => 'publicRead'
        ]);

        $imageUrl = "https://storage.googleapis.com/{$bucket->name()}/{$filePath}";

        $petOwner->update([
            'profile_image' => $imageUrl,
        ]);

        return new PetOwnerResource($petOwner);
    }

    /**
     * Update the pet owner's password.
     */
    public function updatePass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $petOwner = PetOwner::where('email', $request->email)->first();

        if (!$petOwner) {
            return response()->json(['message' => 'Pet owner not found.'], 404);
        }

        $petOwner->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    /**
     * Remove the specified pet owner.
     */
    public function destroy(string $id)
    {
        $petOwner = PetOwner::findOrFail($id);
        $petOwner->delete();
        return response()->json(['message' => 'Pet owner deleted successfully'], 200);
    }
}
