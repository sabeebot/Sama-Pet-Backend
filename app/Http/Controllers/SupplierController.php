<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;




class SupplierController extends Controller
{
    public function index() {
        return SupplierResource::collection(Supplier::all());
    }

    /**
     * Create a new supplier.
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'key_person'   => 'required|string|max:255',
            'contact1'     => 'required|string|max:255',
            'contact2'     => 'nullable|string|max:255',
            'email'        => 'required|email|unique:suppliers,email',
            'address'      => 'required|string|max:255',
            'website'      => 'nullable|string|max:255',
            'instagram'    => 'nullable|string|max:255',
            'profile_image'=> 'nullable|string',  // Base64 encoded image string
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        // Set default image if none is provided (you can change the default as needed)
        $profileImageUrl = 'default.png';

        // Process supplier profile image using Firebase (same as pet/blog)
        if (!empty($data['profile_image']) && preg_match('/^data:image\/(\w+);base64,/', $data['profile_image'], $matches)) {
            $extension = strtolower($matches[1]);
            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                return response()->json(['error' => 'Invalid image type. Only JPG, JPEG, and PNG are allowed.'], 422);
            }
            $imageData = substr($data['profile_image'], strpos($data['profile_image'], ',') + 1);
            $decodedImage = base64_decode($imageData);
            if ($decodedImage === false) {
                return response()->json(['error' => 'Base64 decoding failed.'], 422);
            }
            $fileName = uniqid() . '.' . $extension;
            $firebasePath = 'supplierImage/' . $fileName;
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
                $profileImageUrl = "https://storage.googleapis.com/{$bucket->name()}/{$firebasePath}";
            } catch (\Exception $e) {
                return response()->json(['error' => 'Image upload failed: ' . $e->getMessage()], 500);
            }
        }

        $supplier = Supplier::create(array_merge($data, ['profile_image' => $profileImageUrl]));

        return new SupplierResource($supplier);
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, $id) {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['error' => 'Supplier not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'key_person'   => 'required|string|max:255',
            'contact1'     => 'required|string|max:255',
            'contact2'     => 'nullable|string|max:255',
            'email'        => 'required|email|unique:suppliers,email,' . $id,
            'address'      => 'required|string|max:255',
            'website'      => 'nullable|string|max:255',
            'instagram'    => 'nullable|string|max:255',
            'profile_image'=> 'nullable|string',  // Base64 encoded image string
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        // Start with the current image URL
        $profileImageUrl = $supplier->profile_image;

        // Process new image if provided
        if (!empty($data['profile_image']) && preg_match('/^data:image\/(\w+);base64,/', $data['profile_image'], $matches)) {
            $extension = strtolower($matches[1]);
            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                return response()->json(['error' => 'Invalid image type. Only JPG, JPEG, and PNG are allowed.'], 422);
            }
            $imageData = substr($data['profile_image'], strpos($data['profile_image'], ',') + 1);
            $decodedImage = base64_decode($imageData);
            if ($decodedImage === false) {
                return response()->json(['error' => 'Base64 decoding failed.'], 422);
            }
            $fileName = uniqid() . '.' . $extension;
            $firebasePath = 'supplierImage/' . $fileName;
            $serviceAccountPath = storage_path('app/firebase-auth.json');
            if (!file_exists($serviceAccountPath)) {
                return response()->json(['error' => 'Firebase service account file not found.'], 500);
            }
            try {
                $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
                $storage = $factory->createStorage();
                $bucket = $storage->getBucket();

                $bucket->upload($decodedImage, [
                    'name'          => $firebasePath,
                    'predefinedAcl' => 'publicRead'
                ]);

                $profileImageUrl = "https://storage.googleapis.com/{$bucket->name()}/{$firebasePath}";
            } catch (\Exception $e) {
                return response()->json(['error' => 'Image upload failed: ' . $e->getMessage()], 500);
            }
        }
        // Remove the raw image data from validated data to avoid overwriting
        unset($data['profile_image']);

        $supplier->update(array_merge($data, ['profile_image' => $profileImageUrl]));

        return new SupplierResource($supplier);
    }


public function destroy($id)
{
    $supplier = Supplier::find($id);

    if (!$supplier) {
        return response()->json(['message' => 'Supplier not found'], 404);
    }

    $supplier->delete();

    return response()->json([
        'message' => 'Supplier deleted successfully',
        'deleted' => true
    ], 200);
}






}
