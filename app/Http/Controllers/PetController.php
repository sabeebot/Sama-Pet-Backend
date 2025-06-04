<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Support\Facades\Log;
use App\Models\PetOwner;
use Illuminate\Http\Request;
use App\Http\Resources\PetResource;
use Illuminate\Support\Facades\Validator;
use App\Helpers\FirebaseStorageHelper;
use App\Models\Collar;
use App\Models\LostPets;
use Kreait\Firebase\Factory;
use App\Http\Resources\PetOwnerResource;



class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pets = Pet::all();
        return PetResource::collection($pets);
    }


    


    
    public function getPetsForMating()
{
    // For example, return pets that are available for mating:
    // (Assuming 'no' means not neutered and available for mating.)
    $pets = Pet::where('is_neutered', '1')->get();
    return response()->json($pets);
}

public function getPetsForLost()
{
    // Fetch pets where is_lost is true.
    $pets = Pet::where('is_lost', true)->get();

    // Optionally, you could use a resource collection if you have one, e.g.:
    // return PetResource::collection($pets);

    return response()->json($pets, 200);
}

public function getPetsForSelling()
{
    // Fetch pets where allow_selling is true.
    $pets = Pet::where('allow_selling', true)->get();

    // Return the pets as a JSON response.
    return response()->json($pets, 200);
}




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function getOwnerById($ownerId)
{
    // Fetch the owner details based on the provided ID
    $owner = PetOwner::find($ownerId);

    if (!$owner) {
        return response()->json(['error' => 'Owner not found'], 404);
    }

    // Return owner details using PetOwnerResource
    return new PetOwnerResource($owner);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $debugLogs = [];
    $debugLogs[] = "Store method called.";
    $debugLogs[] = "Request data: " . json_encode($request->all());

    $validator = Validator::make($request->all(), [
        'name'           => 'required|string|max:32',
        'age'            => 'required|int',
        'price'          => 'nullable|numeric|min:0',
        'weight'         => 'required|numeric|min:0',
        'height'         => 'required|numeric|min:0',
        'pet_type'       => 'required|string|max:32',
        'breed'          => 'required|string|max:32',
        'description'    => 'nullable|string|max:1000',
        'color'          => 'required|string|max:32',
        'gender'         => 'required|string|max:32',
        'image'          => 'nullable|string', // Expecting a Base64 image string
        'is_vaccinated'  => 'required|boolean',
        'is_microchipped'=> 'required|boolean',
        'is_neutered'    => 'required|boolean',
        'is_lost'        => 'nullable|boolean',
        'allow_adoption' => 'nullable|boolean',
        'allow_selling'  => 'nullable|boolean',
        'documents'      => 'nullable|array',
        'documents.*.documentTitle' => 'nullable|string|max:255',
        'documents.*.uploadedFile'  => 'nullable|file',
        'pet_owner_id'   => 'required|exists:pet_owners,id',
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors()->toArray();
        $debugLogs[] = "Validation errors: " . json_encode($errors);
        return response()->json(['errors' => $errors, 'debug' => $debugLogs], 422);
    }
    $debugLogs[] = "Validation passed.";

    // ----- Process pet image (Base64 handling similar to PetOwnerController) -----
    $filePath = null;
    $imageData = $request->input('image'); // Expecting a Base64 encoded image string

    if ($imageData && preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
        $extension = strtolower($matches[1]); // e.g., jpg, jpeg, png
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return response()->json(['error' => 'Invalid image type. Only JPG, JPEG, and PNG are allowed.'], 422);
        }
        // Remove the data URL part
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $decodedImage = base64_decode($imageData);
        if ($decodedImage === false) {
            return response()->json(['error' => 'Base64 decoding failed.'], 422);
        }
        // Generate a unique filename and define the Firebase storage path
        $fileName = uniqid() . '.' . $extension;
        $firebasePath = 'petImage/' . $fileName;

        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase service account file not found.'], 500);
        }
        try {
            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();

            // Upload the decoded image data to Firebase Storage with public-read access
            $bucket->upload($decodedImage, [
                'name'          => $firebasePath,
                'predefinedAcl' => 'publicRead'
            ]);

            // Construct the public URL for the uploaded image.
            $filePath = "https://storage.googleapis.com/{$bucket->name()}/{$firebasePath}";
            $debugLogs[] = "Firebase upload succeeded. File path: " . $filePath;
        } catch (\Exception $e) {
            $debugLogs[] = "Firebase upload failed: " . $e->getMessage();
            return response()->json(['error' => $e->getMessage(), 'debug' => $debugLogs], 500);
        }
    } elseif ($request->hasFile('image')) {
        // Fallback: process file upload if a file is provided.
        try {
            $filePath = FirebaseStorageHelper::uploadFile($request->file('image'), 'petImage');
            $debugLogs[] = "Firebase file upload succeeded. File path: " . $filePath;
        } catch (\Exception $e) {
            $debugLogs[] = "Firebase file upload failed: " . $e->getMessage();
            return response()->json(['error' => $e->getMessage(), 'debug' => $debugLogs], 500);
        }
    } else {
        $debugLogs[] = "No image provided.";
    }
    // ------------------------------------------------------------------------------

    // Process document files if present
    $documentsPaths = [];
    if ($request->has('documents')) {
        $debugLogs[] = "Documents found in request.";
        foreach ($request->documents as $document) {
            if (isset($document['uploadedFile'])) {
                try {
                    $documentPath = FirebaseStorageHelper::uploadFile($document['uploadedFile'], 'petDocuments');
                    $documentsPaths[$document['documentTitle']] = $documentPath;
                    $debugLogs[] = "Uploaded document '{$document['documentTitle']}' to: " . $documentPath;
                } catch (\Exception $e) {
                    $debugLogs[] = "Document upload failed for '{$document['documentTitle']}': " . $e->getMessage();
                    return response()->json(['error' => $e->getMessage(), 'debug' => $debugLogs], 500);
                }
            }
        }
    } else {
        $debugLogs[] = "No documents provided.";
    }

    try {
        $pet = Pet::create([
            'name'            => $request->name,
            'age'             => $request->age,
            'weight'          => $request->weight,
            'height'          => $request->height,
            'pet_type'        => $request->pet_type,
            'breed'           => $request->breed,
            'color'           => $request->color,
            'image'           => $filePath,  // Saved image URL (or null)
            'gender'          => $request->gender,
            'is_vaccinated'   => $request->is_vaccinated,
            'is_microchipped' => $request->is_microchipped,
            'is_neutered'     => $request->is_neutered,
            'price'           => $request->price,
            'description'     => $request->description,
            'pet_owner_id'    => $request->pet_owner_id,
            'documents'       => json_encode($documentsPaths),
        ]);
        $debugLogs[] = "Pet record created with ID: " . $pet->id;
    } catch (\Exception $e) {
        $debugLogs[] = "Pet creation failed: " . $e->getMessage();
        return response()->json(['error' => $e->getMessage(), 'debug' => $debugLogs], 500);
    }

    $response = new PetResource($pet);
    return response()->json(['message' => 'Pet created successfully', 'pet' => $response, 'debug' => $debugLogs], 201);
}



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pet = Pet::with('membership')->findOrFail($id);
        return response()->json($pet);
    }


    public function updatePetOwner(Request $request, $petId)
    {
        Log::info('[API] updatePetOwner called', [
            'pet_id' => $petId,
            'incoming_data' => $request->all()
        ]);
    
        $ownerId = $request->input('pet_owner_id');
        if (!$ownerId) {
            Log::warning('[API] Missing pet_owner_id');
            return response()->json(['error' => 'Pet owner ID is required'], 400);
        }
    
        $pet = Pet::find($petId);
        if (!$pet) {
            Log::warning('[API] Pet not found', ['id' => $petId]);
            return response()->json(['error' => 'Pet not found'], 404);
        }
    
        $pet->pet_owner_id = $ownerId;
        $pet->save();
    
        Log::info('[API] Pet owner updated successfully', [
            'pet_id' => $pet->id,
            'new_owner_id' => $ownerId
        ]);
    
        return response()->json(['message' => 'Pet ownership updated successfully']);
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
    public function update(Request $request, $id)
    {


        Log::info('Requested Data: ', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:32',
            'age' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'pet_type' => 'required|string|max:32',
            'breed' => 'required|string|max:32',
            'color' => 'required|string|max:32',
            'gender' => 'required|string|max:32',
            'is_vaccinated' => 'required|string|max:15',
            'is_microchipped' => 'required|string|max:15',
            'is_neutered' => 'required|string|max:15',
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'price' => 'nullable|numeric|min:0', 
            'description' => 'nullable|string|max:1000', 
            'image' => 'nullable|file|image',
        ]);

        // if ($validator->fails()) {
        //     Log::info('Validation Errors:', $validator->errors()->toArray());
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }


        $pet = Pet::findOrFail($id);

        // try {
        //     FirebaseStorageHelper::deleteFile($pet->image);
        // } catch (\Exception $e) {
        //     // Log the exception if necessary, or just ignore it
        //     Log::warning('Image deletion failed: ' . $e->getMessage());
        // }


        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            if ($imageFile) {
                // Upload the new image only once and set it on the pet record
                $imagePath = FirebaseStorageHelper::uploadFile($imageFile, 'petImage');
                $pet->image = $imagePath;
            } else {
                Log::warning('No image file uploaded.');
            }
        }
        
        


        // Update pet details
        $pet->name = $request->name;
        $pet->age = $request->age;
        $pet->weight = $request->weight;
        $pet->height = $request->height;
        $pet->pet_type = $request->pet_type;
        $pet->breed = $request->breed;
        $pet->color = $request->color;
        $pet->gender = $request->gender;
        $pet->is_vaccinated = $request->is_vaccinated;
        $pet->is_microchipped = $request->is_microchipped;
        $pet->is_neutered = $request->is_neutered;
        $pet->pet_owner_id = $request->pet_owner_id;
        $pet->description = $request->description;
        $pet->price = $request->price;
        

        $pet->save();

        return response()->json(['message' => 'Pet updated successfully', 'pet' => $pet], 200);
    }


    public function getFileUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $filePath = $request->file_path;
            Log::info('Requested file path: ' . $filePath);  // <-- Logging example

            $url = FirebaseStorageHelper::getSignedUrl($filePath);
            Log::info('Generated signed URL: ' . $url);  // <-- Logging example

            return response()->json(['url' => $url], 200);
        } catch (\Exception $e) {
            Log::error('Error generating signed URL: ' . $e->getMessage());  // <-- Logging example
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function addDocument(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'documentTitle' => 'required|string|max:255',
        'uploadedFile'  => 'required|file',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $pet = Pet::findOrFail($id);

    try {
        $documentPath = FirebaseStorageHelper::uploadFile($request->file('uploadedFile'), 'petDocuments');
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }

    // Retrieve current documents; if null, start with an empty array
    $documents = [];

if (is_array($pet->documents)) {
    $documents = $pet->documents;
} elseif (is_string($pet->documents) && !empty($pet->documents)) {
    $decoded = json_decode($pet->documents, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $documents = $decoded;
    }
}


    // Append a new document object. You can add more metadata if needed.
    $documents[] = [
        'documentTitle' => $request->documentTitle,
        'documentPath'  => $documentPath,
        'uploaded_at'   => now()->toDateTimeString(),
    ];

    // Save the updated documents array back to the pet model
    $pet->documents = $documents;
    $pet->save();

    return response()->json([
        'message'   => 'Document added successfully',
        'documents' => $pet->documents
    ], 200);
}



    public function updateLostStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'is_lost' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pet = Pet::findOrFail($id);
        $pet->is_lost = $request->is_lost;
        $pet->save();

        return response()->json(['message' => 'Pet status updated successfully', 'pet' => $pet], 200);
    }


    public function updateAdoptionStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'allow_adoption' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pet = Pet::findOrFail($id);
        $pet->allow_adoption = $request->allow_adoption;
        $pet->save();

        return response()->json(['message' => 'Pet adoption status updated successfully', 'pet' => $pet], 200);
    }

    public function updateSellingStatus(Request $request, $id)
    {
    $validator = Validator::make($request->all(), [
        'allow_selling' => 'required|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $pet = Pet::findOrFail($id);
    $pet->allow_selling = $request->allow_selling;
    $pet->save();

    return response()->json(['message' => 'Pet selling status updated successfully', 'pet' => $pet], 200);
    }

    public function updateMatingStatus(Request $request, $id)
    {

        Log::info('updateMatingStatus called', $request->all());
        Log::info('Request value:', ['is_neutered' => $request->input('is_neutered')]);

        $validator = Validator::make($request->all(), [
            'is_neutered' => 'required|in:0,1',  // Expecting 'yes' or 'no'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $pet = Pet::findOrFail($id);
    
        // Directly update is_neutered based on request value
        $pet->is_neutered = $request->is_neutered;
        $pet->save();
    
        return response()->json(['message' => 'Pet mating status updated successfully', 'pet' => $pet], 200);
    }

    public function deleteImage($id)
    {
        $pet = Pet::findOrFail($id);

        // Delete the image from storage
        if ($pet->image) {
            FirebaseStorageHelper::deleteFile($pet->image);
            $pet->image = null;
            $pet->save();
        }

        return response()->json(['message' => 'Image deleted successfully']);
    }

    public function updateImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'uploadedFile' => 'required|file|image',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $pet = Pet::findOrFail($id);
    
        // Delete the old image if it exists
        if ($pet->image) {
            try {
                FirebaseStorageHelper::deleteFile($pet->image);
            } catch (\Exception $e) {
                // Log the exception if necessary, or just ignore it
                Log::warning('Image deletion failed: ' . $e->getMessage());
            }
        }
    
        // Upload the new image
        $imagePath = FirebaseStorageHelper::uploadFile($request->file('uploadedFile'), 'petImage');
        $pet->image = $imagePath;
        $pet->save();
    
        return response()->json(['message' => 'Image updated successfully', 'image' => $imagePath]);
    }

    
    public function markPetAsLost(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);
        $pet->is_lost = true;
        $pet->save();

        return response()->json(['message' => 'Pet marked as lost successfully']);
    }
    public function markPetAsFound(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);
        $pet->is_lost = false;
        $pet->save();

        return response()->json(['message' => 'Pet marked as found successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pet = Pet::findOrFail($id);
        $pet->delete();
        return response()->json(['message' => 'Pet deleted successfully'], 200);
    }

    public function allLostPets()
    {
        $pets = LostPets::all();
        return response()->json(['lostPets' => $pets], 200);
    }
    public function testing(Request $request)
    {
        return response()->json([$request], 200);
    }
    public function addLostPetByFounder(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:32',
            'gender' => 'nullable|string|in:m,f',
            'pet_type' => 'required|string|max:32',
            'breed' => 'required|string|max:32',
            'color' => 'required|string|max:32',
            'image' => 'nullable|file|image',
            'location' => 'required|string',
            'role' => 'required|string',
            'description' => 'nullable|string',
            'pet_owner_id' => 'exists:pet_owners,id', // Pet owner who found the pet
        ]);

        Log::info('Requested Data: ', $request->all());

        if ($validator->fails()) {
            Log::info('validator error: ', $validator->errors());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Process image file if present
        $filePath = null;
        if ($request->hasFile('image')) {
            $serviceAccountPath = storage_path('app/firebase-auth.json');
            if (!file_exists($serviceAccountPath)) {
                return response()->json(['error' => 'Firebase service account file does not exist at path: ' . $serviceAccountPath], 500);
            }

            $factory = (new Factory)->withServiceAccount($serviceAccountPath);
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();

            $profileImage = $request->file('image');
            $fileName = $profileImage->getClientOriginalName();
            $firebaseFilePath = 'lost_pet_images/' . uniqid() . '_' . $fileName;

            $firebaseFile = fopen($profileImage->getPathname(), 'r');
            $bucket->upload($firebaseFile, [
                'name' => $firebaseFilePath,
                'predefinedAcl' => 'publicRead'
            ]);

            $filePath = "https://storage.googleapis.com/{$bucket->name()}/{$firebaseFilePath}";
        }

        try {
            $lostPet = LostPets::create([
                'name' => $request->name,
                'gender' => $request->gender,
                'pet_type' => $request->pet_type,
                'breed' => $request->breed,
                'color' => $request->color,
                'image' => $filePath,
                'location' => $request->location,
                'description' => $request->description,
                'pet_owner_id' => $request->pet_owner_id, // Founder
                'role' => $request->role,
            ]);

            return response()->json(['message' => 'Lost pet reported successfully', 'lostPet' => $lostPet], 201);
        } catch (\Exception $e) {
            Log::error('Error creating lost pet: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to report lost pet.'], 500);
        }
    }

    public function showLostPetByFounder($id)
    {
        $lostPet = LostPets::find($id);

        if (!$lostPet) {
            return response()->json(['error' => 'Lost pet not found'], 404);
        }

        return response()->json(['lostPet' => $lostPet], 200);
    }


    public function deleteLostPetByFounder($id)
    {
        $lostPet = LostPets::find($id);

        if (!$lostPet) {
            return response()->json(['error' => 'Lost pet not found'], 404);
        }

        // Delete the image from storage if exists
        if ($lostPet->image) {
            FirebaseStorageHelper::deleteFile($lostPet->image);
        }

        $lostPet->delete();

        return response()->json(['message' => 'Lost pet report deleted successfully'], 200);
    }

    public function getPetsByOwnerId($petOwnerId)
    {
        $pets = Pet::where('pet_owner_id', $petOwnerId)->get();
        return response()->json(['pets' => $pets], 200);
    }


    public function getPetsWithMembershipByOwner($ownerId)
    {
        // Fetch pets owned by the given ownerId
        $pets = Pet::where('pet_owner_id', $ownerId)
            ->whereHas('membership') // Ensure the pet has an associated membership
            ->with(['membership.package']) // Eager load membership and related package information
            ->get();
    
        // Format the data to include package title directly in the response
        $petsWithPackageNames = $pets->map(function ($pet) {
            $pet->membership->each(function ($membership) {
                // Add package title to each membership
                $membership->packageName = $membership->package->title;
            });
            return $pet;
        });
    
        return response()->json($petsWithPackageNames);
    }

    public function getPetsForAdoption()
    {
        // Fetch pets that are available for adoption
        $pets = Pet::where('allow_adoption', true)->get();

        // Return the pets as a JSON response
        return response()->json($pets);
    }

}
