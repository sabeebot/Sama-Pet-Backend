<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\FirebaseStorageHelper;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {


        try {
            $services = Service::all();
    
            return response()->json($services, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($providerId)
    {
        // Fetch all services where provider_id matches the given ID
        $services = Service::where('provider_id', $providerId)->get();
       // Add the full image URL for each provider
       $services = $services->map(function ($service) {
        if (!empty($service->image)) {
            try {
                $service->service_image_url = FirebaseStorageHelper::getSignedUrl($service->image);
            } catch (\Exception $e) {
                $service->service_image_url = null; // Handle missing or invalid image paths gracefully
            }
        } else {
            $service->service_image_url = null; // No image available
        }
        return $service;
    });
        // Return the list of services as JSON
        return response()->json($services);
    }
    public function shows($serviceid)
    {
        // Fetch all services where provider_id matches the given ID
        $services = Service::find($serviceid);
        // Return the list of services as JSON
        return response()->json($services);
    }
    

    public function getServicesByProviderId($provider_id)
    {
       
        // Fetch all services where provider_id matches the given ID
        $services = Service::where('provider_id', $provider_id)->get();

        // Return the list of services as JSON
        return response()->json($services);
    }

    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            FirebaseStorageHelper::deleteFile($service->image);
            $service->delete();
            return response()->json(['message' => 'Service deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting service: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        // return response()->json($request, 200);
        try {
            // Validate the request data first
            $validator = Validator::make($request->all(), [
                'Service.serviceNameEng' => 'required|string|max:128',
                'Service.serviceDescriptionEn' => 'required|string|max:256',
                'Service.priceBefore' => 'required|numeric',
                'Service.priceAfter' => 'nullable|numeric',
                'Service.discount' => 'nullable|integer',
                'Service.profileId' => 'required|integer',  // Ensure provider exists
                'Service.serviceDescriptionAr' => 'required|string',
                'Service.serviceNameAra' => 'required|string',
                'Service.imageUrl' => 'required|string', // Adjusted to check within Service
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Initialize imagePath
            $imagePath = null;
    
            // Handle image upload
            $base64Image = $request->input('Service.imageUrl'); // Accessing imageUrl from Service
            if (!empty($base64Image)) {
                $imagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'serviceDocuments', 'imageUrl' . uniqid() . '.png');
            }
    
            // Get validated data
            $validatedData = $request->input('Service');
    
            // Prepare service data
            $service = [
                'title' => $validatedData['serviceNameEng'],
                'title_ar' => $validatedData['serviceNameAra'] ?? null,
                'short_description' => $validatedData['serviceDescriptionEn'],
                'short_description_ar' => $validatedData['serviceDescriptionAr'] ?? null,
                'old_price' => $validatedData['priceBefore'],
                'new_price' => $validatedData['priceAfter'],
                'percentage' => $validatedData['discount'],
                'image' => $imagePath, // Use uploaded image or null
                'contact_number' => 'null', // Set to null or appropriate value
                'pet_type' => 'null', // Set to null or appropriate value
                'provider_id' => $validatedData['profileId'],
                'status' => 'deactive', 
            ];
    
            // Create the service
            $serviceCreated = Service::create($service);
            if ($serviceCreated) {
                return response()->json(['message' => 'Service added successfully', 'Service' => $serviceCreated], 200);
            } else {
                return response()->json(['message' => 'Failed to add service'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in store method: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }
    public function update_status($id){
        $Service = Service::find($id);
    
        if ($Service) {
           if($Service->status === 'deactive'){
            $status = 'active';
           }else{
            $status = 'deactive';
           }

        }else{
            return response()->json(['message' => 'Service info not found'], 404);
        }
           $Service->update(['status'=>$status]);
        return response()->json(['message' => 'Service status updated successfully'], 200);
    }
    public function update(Request $request, $id)
{
    // Find the existing service by its ID
    $service = Service::find($id);

    // If the service doesn't exist, return a 404 error
    if (!$service) {
        return response()->json(['message' => 'Service not found'], 404);
    }

    // Image Handling
    $imagePath = $service->image; // Default to existing image

    if ($request->has('imageUrl') && !empty($request->input('imageUrl'))) {
        // Delete the old image if exists
        if ($service->image) {
            try {
                if (FirebaseStorageHelper::checkFileExists($service->image)) {
                    FirebaseStorageHelper::deleteFile($service->image);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to delete old image: ' . $e->getMessage());
            }
        }
        $base64Image = $request->input('imageUrl');
        $imagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'serviceDocuments', 'imageUrl_' . uniqid() . '.png');
    } elseif ($request->hasFile('imageUrl')) {
        // Delete the old image if exists
        if ($service->image) {
            try {
                if (FirebaseStorageHelper::checkFileExists($service->image)) {
                    FirebaseStorageHelper::deleteFile($service->image);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to delete old image: ' . $e->getMessage());
            }
        }
        $image = $request->file('imageUrl');
        $imagePath = FirebaseStorageHelper::uploadFile($image, 'serviceDocuments');
    }

    // Log the incoming request data for debugging
    Log::info('Incoming request data: ', $request->all());

    // Access data correctly
    $validatedData = $request->input('Service');

    // Check if validatedData is null
    if (is_null($validatedData)) {
        return response()->json(['message' => 'No service data provided'], 400);
    }

    // Validate the incoming data
    $validator = Validator::make($validatedData, [
        'serviceNameEng' => 'nullable|string|max:128', // Nullable to allow fallback to old data
        'serviceDescriptionEn' => 'nullable|string|max:256',
        'priceBefore' => 'nullable|numeric',
        'priceAfter' => 'nullable|numeric',
        'discount' => 'nullable|integer',
        'profileId' => 'nullable|integer',
        'serviceDescriptionAr' => 'nullable|string',
        'serviceNameAra' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Map the validated data to the service fields, retaining old data for empty fields
    $serviceData = [
        'title' => $validatedData['serviceNameEng'] ?? $service->title,
        'title_ar' => $validatedData['serviceNameAra'] ?? $service->title_ar,
        'short_description' => $validatedData['serviceDescriptionEn'] ?? $service->short_description,
        'short_description_ar' => $validatedData['serviceDescriptionAr'] ?? $service->short_description_ar,
        'old_price' => $validatedData['priceBefore'] ?? $service->old_price,
        'new_price' => $validatedData['priceAfter'] ?? $service->new_price,
        'percentage' => $validatedData['discount'] ?? $service->percentage,
        'provider_id' => $validatedData['profileId'] ?? $service->provider_id,
        'image' => $imagePath,
        'status' => $service->status, // Retain existing status
    ];

    // Update the service
    $serviceUpdated = $service->update($serviceData);

    // Return a success response or error
    if ($serviceUpdated) {
        return response()->json(['message' => 'Service updated successfully', 'service' => $service], 200);
    } else {
        return response()->json(['message' => 'Failed to update service'], 500);
    }
}





    public function deleteAllByProvider($providerId)
    {
        try {
            // Fetch all services for the given providerId
            $services = Service::where('provider_id', $providerId)->get();
    
            // Check if services exist for the provider
            if ($services->isEmpty()) {
                return response()->json(['message' => 'No services found for this provider.'], 404);
            }
    
            // Iterate through each service and delete
            foreach ($services as $service) {
                // If the service has an image, delete it from Firebase
                if ($service->image && $service->image != 'null') {
                    try {
                        FirebaseStorageHelper::deleteFile($service->image);
                    } catch (\Exception $e) {
                        Log::error('Error deleting service image: ' . $e->getMessage());
                    }
                }
    
                // Delete the service from the database
                $service->delete();
            }
    
            // Return success message
            return response()->json(['message' => 'All services for the provider have been deleted successfully.'], 200);
    
        } catch (\Exception $e) {
            // Handle any error that occurs during the deletion process
            return response()->json(['message' => 'Error deleting services: ' . $e->getMessage()], 500);
        }
    }

    
}
