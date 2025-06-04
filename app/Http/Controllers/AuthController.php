<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PetOwner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PetOwnerResource;
use App\Http\Resources\PetResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $nationalities = ['Bahrain', 'USA', 'Canada', 'UK', 'Australia', 'Other'];


    public function profile()
    {
        $user = Auth::user();
        return new PetOwnerResource($user);
    }

    public function pets()
    {
        $user = Auth::user();
        $pets = $user->pets;
        return PetResource::collection($pets);
    }
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exists = PetOwner::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists], 200);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|alpha|min:3|max:31',
            'last_name' => 'required|alpha|min:3|max:31',
            'gender' => 'required|in:m,f',
            'email' => 'required|email:rfc,dns|unique:pet_owners',
            'password' => [
                'required',
                Password::min(8)->letters()->numbers(),
            ],
            'nationality' => 'required|in:' . implode(',', $this->nationalities),
            'phone' => 'required|numeric|digits:8',
            'profile_image' => 'nullable|file|image|max:2048', // Image is now nullable
            'city' => 'string|max:32',
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

        $profileImageUrl = null;

        if ($request->hasFile('profile_image')) {
            $serviceAccountPath = storage_path('app\\firebase-auth.json');
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

            $profileImageUrl = "https://storage.googleapis.com/{$bucket->name()}/{$filePath}";
        }

        $petOwner = PetOwner::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nationality' => $request->nationality,
            'phone' => $request->phone,
            'profile_image' => $profileImageUrl, // Save the image URL or null
            'city' => $request->city,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'house' => $request->house,
            'road' => $request->road,
            'block' => $request->block,
            'building' => $request->building_name,
            'apt_number' => $request->apt_number,
            'floor' => $request->floor,
            'company' => $request->company,
        ]);
        $token = $petOwner->createToken('authToken')->plainTextToken;
        return response()->json(['message' => 'Account created successfully', 'token' => $token, 'pet_owner' => $petOwner], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $petOwner = PetOwner::where('email', $request->email)->first();

        if (!$petOwner || !Hash::check($request->password, $petOwner->password)) {
            return response()->json(['message' => "Invalid email or password."], 401);
        }

        $token = $petOwner->createToken('authToken', ['user'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $petOwner
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function test(Request $request)
    {
        // return '';
        if ($request->user()) {
            return response()->json(['message' => 'You are authenticated'], 200);
        }

        return response()->json(['message' => 'You are not authenticated'], 401);
    }
}
