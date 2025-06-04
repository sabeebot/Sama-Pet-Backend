<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\FirebaseStorageHelper;
use Exception; // Import the base Exception class
use Illuminate\Support\Facades\Log;



class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($provider_id)
    {
        $galleries = Gallery::where('provider_id', $provider_id)->get();
        return response()->json($galleries);
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

        try
        {
            if ($request->has('images')) {
                $imagePaths = []; // Array to store the uploaded image paths
                foreach ($request->images as $key => $image) {
                    if (!empty($image) && !is_file($image)) {
                        // If the image is a Base64 string
                        $imagePath = FirebaseStorageHelper::uploadBase64Image(
                            $image,
                            'GalleryData',
                            'gallery_image_' . $key . '_' . uniqid() . '.png'
                        );
                    } elseif (is_file($image)) {
                        // If the image is a regular file
                        $imagePath = FirebaseStorageHelper::uploadFile($image, 'GalleryData');
                    } else {
                        continue; // Skip if the image is invalid
                    }
                    $imagePaths[] = $imagePath; // Store the image path in the array
                }
                
                // Do something with $imagePaths, like saving in database
            }
            
            // Convert the array to JSON
            $imagePathsJson = json_encode($imagePaths);
    
            $BannerimagePath = null; // Default null if no image provided
         // Handle base64 image upload if it's a string
    if ($request->has('banner') && !empty($request->input('banner'))) {
        $base64Image = $request->input('banner');
        // Apply uploadBase64Image if it's a base64 string
        $BannerimagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'GalleryData', 'banner_image_' . uniqid() . '.png');
    } elseif ($request->hasFile('banner')) {
        // Apply uploadFile if it's a regular file
        $image = $request->file('banner');
        $BannerimagePath = FirebaseStorageHelper::uploadFile($image, 'GalleryData');
    }
    
    $contractPath = null; // Default null if no image provided
    // Handle base64 image upload if it's a string
    if ($request->has('contract') && !empty($request->input('contract'))) {
    $base64Image = $request->input('contract');
    // Apply uploadBase64Image if it's a base64 string
    $contractPath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'GalleryData', 'contract_' . uniqid() . '.png');
    } elseif ($request->hasFile('contract')) {
    // Apply uploadFile if it's a regular file
    $image = $request->file('contract');
    $contractPath = FirebaseStorageHelper::uploadFile($image, 'GalleryData');
    }
    
    
    $documentPath = null; // Default null if no image provided
    // Handle base64 image upload if it's a string
    if ($request->has('document') && !empty($request->input('document'))) {
    $base64Image = $request->input('document');
    // Apply uploadBase64Image if it's a base64 string
    $documentPath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'GalleryData', 'document_' . uniqid() . '.pdf');
    } elseif ($request->hasFile('document')) {
    // Apply uploadFile if it's a regular file
    $image = $request->file('document');
    $documentPath = FirebaseStorageHelper::uploadFile($image, 'GalleryData');
    }
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'typeEN' => 'required|string',
                'typeAR' => 'required|string',
                'descriptionEN' => 'required|string',
                'descriptionAR' => 'required|string',
                'provider_id' => 'required|integer',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        
            // Prepare the gallery data
            $gallery = [
                'provider_id' => $request->input('provider_id'),
                'type_en' => $request->input('typeEN'),
                'type_ar' => $request->input('typeAR'),
                'description_en' => $request->input('descriptionEN'),
                'description_ar' => $request->input('descriptionAR'),
                'image_url'=> $imagePathsJson,
                'banner'=>$BannerimagePath,
                'contract'=>$contractPath,
                'document'=>$documentPath,
            ];
        
            // Handle file uploads
            // if ($request->hasFile('banner')) {
            //     $gallery['banner'] = $request->file('banner')->store('banners', 'public');
            // }
        
            // if ($request->hasFile('contract')) {
            //     $gallery['contract'] = $request->file('contract')->store('contracts', 'public');
            // }
        
            // if ($request->hasFile('document')) {
            //     $gallery['document'] = $request->file('document')->store('documents', 'public');
            // }
        
            // if ($request->hasFile('images')) {
            //     $imagePaths = [];
            //     foreach ($request->file('images') as $image) {
            //         $imagePaths[] = $image->store('images', 'public');
            //     }
            //     // Store the first image's URL in the image_url field (if required)
            //     $gallery['image_url'] = $imagePaths[0] ?? null;
            //     $gallery['images'] = $imagePaths; // Add all image paths for reference
            // }
        
            // Return response with the prepared data before saving
         
        
            // Uncomment below lines if you want to save the data after returning the preview
            
            $GalleryStore = Gallery::create($gallery);
        
            if ($GalleryStore) {
                return response()->json(['message' => 'Gallery added successfully', 'data' => $GalleryStore], 201);
            } else {
                return response()->json(['message' => 'Failed to add Gallery'], 500);
            }

        } catch (Exception $e) {
            // Log the exception for backend analysis (or use Laravel's Log facade)
            Log::error('Error in storing gallery: ' . $e->getMessage());
    
            // Return the error message to the frontend
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }
    
    
    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $Gallery = Gallery::findOrFail($id);
        return response()->json($Gallery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    Log::info("hello");
    Log::info('Request data: ' . json_encode($request->all()));

    try {
        
        // Fetch existing gallery data
        $gallery = Gallery::findOrFail($id);

        // Initialize variables for new paths with existing values
        $imagePaths = json_decode($gallery->image_url, true) ?? [];
        $bannerPath = $gallery->banner;
        $contractPath = $gallery->contract;
        $documentPath = $gallery->document;
        
        // Handle image updates (check if the images field is not empty)
        if ($request->has('Gallery.images') && !empty($request->Gallery['images'])) {
            $imageUrls = is_string($request->Gallery['images']) 
                ? json_decode($request->Gallery['images'], true) 
                : $request->Gallery['images'];

                Log::info('Incoming images: ' . json_encode($imageUrls));


                
            // Delete previous images if any
            foreach ($imagePaths as $path) {
                FirebaseStorageHelper::deleteFile($path);
            }
            $imagePaths = [];  // Reset image paths array

            // Upload new images
            foreach ($imageUrls as $image) {
                if (!empty($image) && !is_file($image)) {
                    $imagePaths[] = FirebaseStorageHelper::uploadBase64Image(
                        $image,
                        'GalleryData',
                        'gallery_image_' . uniqid() . '.png'
                    );
                } elseif (is_file($image)) {
                    $imagePaths[] = FirebaseStorageHelper::uploadFile($image, 'GalleryData');
                    Log::info('Incoming images: ' . json_encode($imageUrls));
                }
            }
        }

        // Handle banner update (same logic for the banner)
        if ($request->has('Gallery.banner') && !empty($request->Gallery['banner'])) {
            if ($bannerPath) {
                FirebaseStorageHelper::deleteFile($bannerPath);
            }
            $bannerPath = FirebaseStorageHelper::uploadBase64Image(
                $request->Gallery['banner'],
                'GalleryData',
                'banner_image_' . uniqid() . '.png'
            );
        } elseif ($request->hasFile('Gallery.banner')) {
            if ($bannerPath) {
                FirebaseStorageHelper::deleteFile($bannerPath);
            }
            $bannerPath = FirebaseStorageHelper::uploadFile($request->file('Gallery.banner'), 'GalleryData');
        } else {
            // If banner is empty, delete the existing one
            if ($bannerPath) {
                FirebaseStorageHelper::deleteFile($bannerPath);
                $bannerPath = null;
            }
        }

        // Handle contract update
        if ($request->has('Gallery.contract') && !empty($request->Gallery['contract'])) {
            if ($contractPath) {
                FirebaseStorageHelper::deleteFile($contractPath);
            }
            $contractPath = FirebaseStorageHelper::uploadBase64Image(
                $request->Gallery['contract'],
                'GalleryData',
                'contract_' . uniqid() . '.png'
            );
        } elseif ($request->hasFile('Gallery.contract')) {
            if ($contractPath) {
                FirebaseStorageHelper::deleteFile($contractPath);
            }
            $contractPath = FirebaseStorageHelper::uploadFile($request->file('Gallery.contract'), 'GalleryData');
        } else {
            if ($contractPath) {
                FirebaseStorageHelper::deleteFile($contractPath);
                $contractPath = null;
            }
        }

        // Handle document update (same logic)
        if ($request->has('Gallery.document') && !empty($request->Gallery['document'])) {
            if ($documentPath) {
                FirebaseStorageHelper::deleteFile($documentPath);
            }
            $documentPath = FirebaseStorageHelper::uploadBase64Image(
                $request->Gallery['document'],
                'GalleryData',
                'document_' . uniqid() . '.pdf'
            );
        } elseif ($request->hasFile('Gallery.document')) {
            if ($documentPath) {
                FirebaseStorageHelper::deleteFile($documentPath);
            }
            $documentPath = FirebaseStorageHelper::uploadFile($request->file('Gallery.document'), 'GalleryData');
        } else {
            if ($documentPath) {
                FirebaseStorageHelper::deleteFile($documentPath);
                $documentPath = null;
            }
        }

        // Update the database, keeping old values for empty fields
        $gallery->update([
            'type_en' => $request->Gallery['typeEN'] ?? $gallery->type_en,
            'type_ar' => $request->Gallery['typeAR'] ?? $gallery->type_ar,
            'description_en' => $request->Gallery['descriptionEN'] ?? $gallery->description_en,
            'description_ar' => $request->Gallery['descriptionAR'] ?? $gallery->description_ar,
            'provider_id' => $request->Gallery['provider_id'] ?? $gallery->provider_id,
            'image_url' => json_encode($imagePaths),
            'banner' => $bannerPath,
            'contract' => $contractPath,
            'document' => $documentPath,
        ]);

        return response()->json(['message' => 'Gallery updated successfully', 'data' => $gallery], 200);
    } catch (Exception $e) {
        // Log detailed error message to Laravel logs
        Log::error('Error updating gallery: ' . $e->getMessage(), [
            'stack' => $e->getTraceAsString(),
            'request_data' => $request->all(),
            'gallery_id' => $id,
        ]);
        
        // Send the full stack trace to the frontend
        return response()->json([
            'message' => 'Failed to update Gallery',
            'error' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
            'request_data' => $request->all()  // Optional: log the request data for context
        ], 500);
    }
}


    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    // Find the gallery entry by ID
    $gallery = Gallery::find($id);

    // Check if the gallery exists
    if ($gallery) {
        // Delete images from Firebase if they exist
        if ($gallery->image_url) {
            $imagePaths = json_decode($gallery->image_url);
            foreach ($imagePaths as $imagePath) {
                try {
                    FirebaseStorageHelper::deleteFile($imagePath); // Delete each image from Firebase
                } catch (Exception $e) {
                    return response()->json(['message' => 'Failed to delete image from Firebase: ' . $e->getMessage()], 500);
                }
            }
        }

        // Delete other files from Firebase if they exist
        try {
            if ($gallery->banner) {
                FirebaseStorageHelper::deleteFile($gallery->banner);
            }
            if ($gallery->contract) {
                FirebaseStorageHelper::deleteFile($gallery->contract);
            }
            if ($gallery->document) {
                FirebaseStorageHelper::deleteFile($gallery->document);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete file from Firebase: ' . $e->getMessage()], 500);
        }

        // Delete the gallery entry from the database
        $gallery->delete();

        // Return success response
        return response()->json(['message' => 'Gallery deleted successfully'], 200);
    }

    // If the gallery was not found
    return response()->json(['message' => 'Gallery not found'], 404);
}

}