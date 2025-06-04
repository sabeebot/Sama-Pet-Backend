<?php

namespace App\Http\Controllers;

use App\Models\Blogs;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Blogs::all());
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate only the fields sent from the Angular form
        $validatedData = $request->validate([
            'title'       => 'required|string|max:128',
            'description' => 'required|string|max:256',
            'petType'     => 'required|string|max:64',
            'image'       => 'nullable|string', // Base64 image string is optional
        ]);

        // Initialize variable for blog image URL.
    $blogImageURL = '';

    if(isset($validatedData['image']) && preg_match('/^data:image\/(\w+);base64,/', $validatedData['image'], $matches)) {
        $extension = strtolower($matches[1]); // jpg, jpeg, or png
        if(!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return response()->json(['error' => 'Invalid image type. Only JPG, JPEG, and PNG are allowed.'], 422);
        }
        // Remove data URL prefix.
        $imageData = substr($validatedData['image'], strpos($validatedData['image'], ',') + 1);
        $decodedImage = base64_decode($imageData);
        if($decodedImage === false) {
            return response()->json(['error' => 'Base64 decoding failed.'], 422);
        }
        // Generate unique file name and path.
        $fileName = uniqid() . '.' . $extension;
        $firebasePath = 'blogImage/' . $fileName;
        
        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase service account file not found.'], 500);
        }
        
        try {
            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();
    
            // Upload the decoded image data to Firebase Storage.
            $bucket->upload($decodedImage, [
                'name' => $firebasePath,
                'predefinedAcl' => 'publicRead'
            ]);
    
            // Construct public URL.
            $blogImageURL = "https://storage.googleapis.com/{$bucket->name()}/{$firebasePath}";
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image upload failed: ' . $e->getMessage()], 500);
        }
    }

        // Map the petType to tag and set a default image
        $blog = Blogs::create([
            'title'       => $validatedData['title'],
            'description' => $validatedData['description'],
            'tag'         => $validatedData['petType'],
            'image'       => $blogImageURL
        ]);

        return response()->json($blog, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $blog = Blogs::findOrFail($id);
        return response()->json($blog);
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
    public function update(Request $request, string $id)
{
    $blog = Blogs::findOrFail($id);

    // Validate the incoming data. Use "sometimes" so fields are optional on update.
    $validatedData = $request->validate([
        'tag'         => 'sometimes|string|max:128',
        'title'       => 'sometimes|string|max:128',
        'image'       => 'sometimes|string', // Expect a Base64 string if provided
        'description' => 'sometimes|string|max:256',
    ]);

    // Start with the existing blog image URL.
    $blogImageURL = $blog->image;

    // If a new image is provided (in Base64 format), process it.
    if (isset($validatedData['image']) && preg_match('/^data:image\/(\w+);base64,/', $validatedData['image'], $matches)) {
        $extension = strtolower($matches[1]); // e.g., jpg, jpeg, png
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return response()->json(['error' => 'Invalid image type. Only JPG, JPEG, and PNG are allowed.'], 422);
        }
        // Remove the data URL prefix and decode the image.
        $imageData = substr($validatedData['image'], strpos($validatedData['image'], ',') + 1);
        $decodedImage = base64_decode($imageData);
        if ($decodedImage === false) {
            return response()->json(['error' => 'Base64 decoding failed.'], 422);
        }
        // Generate a unique filename and define the Firebase storage path.
        $fileName = uniqid() . '.' . $extension;
        $firebasePath = 'blogImage/' . $fileName;
        
        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase service account file not found.'], 500);
        }
        
        try {
            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();
    
            // Upload the decoded image data to Firebase Storage.
            $bucket->upload($decodedImage, [
                'name'          => $firebasePath,
                'predefinedAcl' => 'publicRead'
            ]);
    
            // Construct the public URL.
            $blogImageURL = "https://storage.googleapis.com/{$bucket->name()}/{$firebasePath}";
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image upload failed: ' . $e->getMessage()], 500);
        }
    }

    // Remove the image field from validatedData to avoid conflict.
    unset($validatedData['image']);

    // Update the blog record with the new data (merging in the updated image URL).
    $blog->update(array_merge($validatedData, ['image' => $blogImageURL]));

    return response()->json($blog);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = Blogs::findOrFail($id);
        $blog->delete();

        return response()->json(null, 204);
    }
}
