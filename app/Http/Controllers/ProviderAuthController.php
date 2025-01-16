<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use App\Models\PendingProvider;

class ProviderAuthController extends Controller
{

    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exists = Provider::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists], 200);
    }

    private $types = ['doctor', 'pet shop', 'groomer', 'pet clinic', 'trainer'];

    public function ProRegister(Request $request)
    {
        $baseValidator = Validator::make($request->all(), [
            'token' => 'required|string|exists:pending_providers,token',
            'years_of_experience' => 'nullable|numeric',
            'medical_degree_and_specialization' => 'nullable|string|max:128',
            'availability' => 'required|array',
            'timing.from' => 'required|date_format:H:i',
            'timing.to' => 'required|date_format:H:i',
            'documents' => 'nullable|array',
            'profile_image' => 'nullable|file|image|max:2048',
            'veterinarians' => 'nullable|array',
            'veterinarians.*.name' => 'required|string|max:64',
            'veterinarians.*.email' => 'required|email|unique:veterinarians,email',
            'veterinarians.*.bio' => 'nullable|string|max:256',
            'veterinarians.*.education' => 'nullable|string|max:64',
            'veterinarians.*.years_of_experience' => 'nullable|numeric|min:0',
            'veterinarians.*.specialization' => 'nullable|string|max:128',
            'veterinarians.*.picture' => 'nullable|file|image|max:2048',
        ]);

        if ($baseValidator->fails()) {
            return response()->json(['errors' => $baseValidator->errors()], 422);
        }

        // Pending provider retrieval
        $pendingProvider = PendingProvider::where('token', $request->token)->first();

        // Firebase profile image upload
        $profileImageUrl = null;
        if ($request->hasFile('profile_image')) {
            // (Firebase upload logic for profile image here)
        }

        // Document upload handling
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $docPath = $document->store('documents');
                $documents[] = asset('storage/' . $docPath);
            }
        }

        // Provider creation in `providers` table
        $provider = Provider::create([
            'name' => $pendingProvider->name,
            'email' => $pendingProvider->email,
            'address' => $pendingProvider->address,
            'contact_no' => $pendingProvider->contact_no,
            'profile_image' => $profileImageUrl,
            'status' => 'pending',
            'type' => $pendingProvider->type,
            'availability' => json_encode($request->availability),
            'timing' => json_encode($request->timing), // Storing timing as JSON
            'social_media' => json_encode($request->social_media ?? []),
            'documents' => json_encode($documents),
        ]);


        // Additional fields for doctors/trainers
        if (in_array($pendingProvider->type, ['doctor', 'trainer'])) {
            $extraInfo = [
                'provider_id' => $provider->id,
                'years_of_experience' => $request->years_of_experience,
                'medical_degree_and_specialization' => $request->medical_degree_and_specialization,
            ];
            if ($pendingProvider->type === 'doctor') {
                \App\Models\DoctorInfo::create($extraInfo);
            } else {
                \App\Models\TrainerInfo::create($extraInfo);
            }
        }

        // Veterinarian info for vet clinics
        if ($pendingProvider->type === 'vetClinic' && !empty($request->veterinarians)) {
            foreach ($request->veterinarians as $vetData) {
                // (Firebase upload for veterinarian picture here, if applicable)
                \App\Models\Veterinarian::create([
                    'provider_id' => $provider->id,
                    'name' => $vetData['name'],
                    'email' => $vetData['email'],
                    'bio' => $vetData['bio'],
                    'education' => $vetData['education'],
                    'years_of_experience' => $vetData['years_of_experience'],
                    'specialization' => $vetData['specialization'],
                    'picture' => $vetData['picture'] ?? null
                ]);
            }
        }

        // Delete pending provider and return token
        $pendingProvider->delete();
        $token = $provider->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Provider account created successfully, awaiting admin approval.',
            'token' => $token,
            'provider' => $provider
        ], 201);
    }


    public function registerPendingProvider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:64',
            'contact_no' => 'required|string|max:8',
            'address' => 'required|string|max:128',
            'type' => 'required|in:doctor,pet shop,groomer,pet clinic,trainer',
            'email' => 'required|email|unique:pending_providers',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $token = Str::random(32);

            // Attempt to create the pending provider
            $pendingProvider = PendingProvider::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                'type' => $request->type,
                'status' => 'pending2',
                'password' => $request->password, // This is already being hashed in the model mutator
                'token' => $token
            ]);

            return response()->json([
                'message' => 'Registration submitted for approval',
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error creating pending provider: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json(['message' => 'Something went wrong on the server.'], 500);
        }
    }

    public function getProviderStatus(Request $request)
    {
        $token = $request->get('token'); // Get the token from the request

        // Find the pending provider by their token
        $pendingProvider = PendingProvider::where('token', $token)->first();

        if (!$pendingProvider) {
            return response()->json(['message' => 'Provider not found'], 404);
        }

        return response()->json(['status' => $pendingProvider->status], 200);
    }


    public function approveProvider($id)
    {
        // Find the pending provider by ID
        $pendingProvider = PendingProvider::find($id);

        if (!$pendingProvider) {
            return response()->json(['message' => 'Pending provider not found'], 404);
        }

        // Move the approved provider to the `providers` table
        $provider = Provider::create([
            'name' => $pendingProvider->name,
            'email' => $pendingProvider->email,
            'contact_no' => $pendingProvider->contact_no,
            'address' => $pendingProvider->address,
            'type' => $pendingProvider->type,
            'password' => $pendingProvider->password, // Already hashed
            'status' => 'approved', // Set status as approved
        ]);

        // Delete the record from the pending_providers table
        $pendingProvider->delete();

        // Return success message
        return response()->json(['message' => 'Provider approved successfully', 'provider' => $provider], 200);
    }


    public function login(Request $request)
    {
        $provider = Provider::where('email', $request->email)->first();
        if (!$provider || !Hash::check($request->password, $provider->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($provider->status !== 'approved') {
            return response()->json(['message' => 'Account is not yet approved'], 403);
        }

        $token = $provider->createToken('authToken')->plainTextToken;
        return response()->json([
            'token' => $token,
            'provider' => $provider
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function profile(Request $request)
    {
        $provider = $request->user();
        return response()->json($provider, 200);
    }

    public function updateProfile(Request $request)
    {
        $provider = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'contact_no' => 'string|max:20',
            'address' => 'string|max:255',
            'timing' => 'json',
            'profile_image' => 'nullable|string',
            'availability' => 'json',
            'social_media' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $provider->update($request->all());

        return response()->json([
            'message' => 'Profile updated successfully',
            'provider' => $provider
        ], 200);
    }
}
