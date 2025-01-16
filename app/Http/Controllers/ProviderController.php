<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Provider;
use App\Models\Promotion;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use App\Models\Gallery;
use App\Models\DoctorInfo;

use Illuminate\Http\Request;
use App\Helpers\encodeImageUrl;
use Illuminate\Support\Facades\Log;
use App\Helpers\FirebaseStorageHelper;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateProviderRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ProviderController extends Controller
{
    public function index()
    {
        try {
            // Get all providers
         // Get all providers with their associated gallery
             $providers = Provider::with('Gallery')->get();
             $totalDoctors = DoctorInfo::count();

            // Get type counts grouped by type
            $typeCounts = Gallery::select('type_en', DB::raw('COUNT(*) as count'))
                ->groupBy('type_en')
                ->get();
    
           
    
            return response()->json([
                'providers' => $providers,
                'type_counts' => $typeCounts,
                'doctors' => $totalDoctors
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Provider $id)
    {

        Log::info('Provider ID: ' . $id);
        $provider = Provider::find($id);
        if ($provider) {
            return response()->json($provider);
        } else {
            return response()->json(['message' => 'Provider not found'], 404);
        }
        
    }
    public function getProductById($id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json($product);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch product', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteProvider($id)
    {

        try {
            // Find the provider by ID or fail
            $provider = Provider::findOrFail($id);
            if($provider->profile_image && $provider->profile_image != "null"){
                FirebaseStorageHelper::deleteFile($provider->profile_image);
            }
            // Delete related services
            $provider->services()->delete();

            // Delete the provider
            $provider->delete();

            return response()->json(['message' => 'Provider and related services deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Provider not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete provider', 'error' => $e->getMessage()], 500);
        }
    }
    public function storeInformation(Request $request)
    {
        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validate image file
                'providerData' => 'required|json', // Validate JSON data
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Parse JSON providerData
            $provider = json_decode($request->input('providerData'), true);
    
            // Handle image upload if exists
            $imagePath = null; // Default null if no image provided
         // Handle base64 image upload if it's a string
    if ($request->has('profile_image') && !empty($request->input('profile_image'))) {
        $base64Image = $request->input('profile_image');
        // Apply uploadBase64Image if it's a base64 string
        $imagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'providerDocuments', 'profile_image_' . uniqid() . '.png');
    } elseif ($request->hasFile('profile_image')) {
        // Apply uploadFile if it's a regular file
        $image = $request->file('profile_image');
        $imagePath = FirebaseStorageHelper::uploadFile($image, 'providerDocuments');
    }else{
        $imagePath = "null";
    }
    
    
            // Prepare provider data
            $providerData = [
                'type' => $provider['providerType'] ?? '',
                'name' => $provider['businessName'] ?? '',
                'contact_no' => $provider['phoneNumber'],
                'email' => $provider['companyEmail'],
                'provider_name_en' => $provider['providerNameEn'],
                'provider_name_ar' => $provider['providerNameAr'],
                'cr_number' => $provider['crNumber'],
                'instagram' => $provider['instagramUrl'],
                'website' => $provider['website'] ?? '',
                'start_date' => $provider['startDate'] ?? '',
                'end_date' => $provider['endDate'] ?? '',
                'password' => 'Null',
                'social_media' => 'Null',
                'documents' => 'Null',
                'office' => $provider['address']['office'] ?? '',
                'road' => $provider['address']['road'] ?? '',
                'block' => $provider['address']['block'] ?? '',
                'city' => $provider['address']['city'] ?? '',
                'status' => 'deactive',
                'availability_days' => json_encode($provider['availabilityDays'] ?? []),
                'availability_hours' => json_encode([
                    'start' => $provider['availabilityHours']['start'] ?? '',
                    'end' => $provider['availabilityHours']['end'] ?? ''
                ]),
                'authorized_persons' => json_encode($provider['authorizedPerson'] ?? []),
                'profile_image' => $imagePath, // Save the uploaded image path
            ];
    
            // Store provider in the database
            $providerStore = Provider::create($providerData);
    
            return response()->json([
                'message' => 'Provider information added successfully. Your application is pending approval.',
                'provider' => $providerStore,
                'profile_image' => $providerStore->profile_image, // Return the saved image URL
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to store provider information: ' . $e->getMessage()], 500);
        }
    }


    public function update_status($id){
        $Provider = Provider::find($id);
       
        if ($Provider) {
           if($Provider->status === 'deactive'){
            $status = 'active';
           }else{
            $status = 'deactive';
           }

        }else{
            return response()->json(['message' => 'Provider info not found'], 404);
        }
        
        $Provider->update(['status'=>$status]);
        $Provider->services()->update(['status' => $status ]);
        $Provider->products()->update(['status' => $status]);
        $Provider->products_catgory()->update(['status' => $status]);
        $Provider->doctorInfo()->update(['status' => $status]);
         
        return response()->json(['message' => 'Provider status updated successfully'], 200);
    }


    public function update(Request $request, $providerId)
    {
        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validate image file
                'providerData' => 'required|json', // Validate JSON data
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Fetch the provider record from the database
            $providerfind = Provider::findOrFail($providerId);
    
            // Parse JSON providerData
            $provider = json_decode($request->input('providerData'), true);
    
            // Handle image upload
            $imagePath = $providerfind->profile_image; // Keep the old path by default
    
            if ($request->has('profile_image') && !empty($request->input('profile_image'))) {
                // Delete the old image from Firebase storage
                if ($providerfind->profile_image) {
                    FirebaseStorageHelper::deleteFile($providerfind->profile_image);
                }
                $base64Image = $request->input('profile_image');
                $imagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'providerDocuments', 'profile_image_' . uniqid() . '.png');
            } elseif ($request->hasFile('profile_image')) {
                // Delete the old image from Firebase storage
                if ($providerfind->profile_image) {
                    FirebaseStorageHelper::deleteFile($providerfind->profile_image);
                }
                $image = $request->file('profile_image');
                $imagePath = FirebaseStorageHelper::uploadFile($image, 'providerDocuments');
            }
    
            // Prepare provider data with fallback to existing values
            $providerData = [
                'type' => $provider['providerType'] ?? $providerfind->type,
                'name' => $provider['businessName'] ?? $providerfind->name,
                'contact_no' => $provider['phoneNumber'] ?? $providerfind->contact_no,
                'email' => $provider['companyEmail'] ?? $providerfind->email,
                'provider_name_en' => $provider['providerNameEn'] ?? $providerfind->provider_name_en,
                'provider_name_ar' => $provider['providerNameAr'] ?? $providerfind->provider_name_ar,
                'cr_number' => $provider['crNumber'] ?? $providerfind->cr_number,
                'instagram' => $provider['instagramUrl'] ?? $providerfind->instagram,
                'website' => $provider['website'] ?? $providerfind->website,
                'start_date' => $provider['startDate'] ?? $providerfind->start_date,
                'end_date' => $provider['endDate'] ?? $providerfind->end_date,
                'password' => $providerfind->password, // Password remains unchanged
                'social_media' => $providerfind->social_media, // Social media remains unchanged
                'documents' => $providerfind->documents, // Documents remain unchanged
                'office' => $provider['address']['office'] ?? $providerfind->office,
                'road' => $provider['address']['road'] ?? $providerfind->road,
                'block' => $provider['address']['block'] ?? $providerfind->block,
                'city' => $provider['address']['city'] ?? $providerfind->city,
                'status' => $providerfind->status ?? 'pending', // Status defaults to 'pending'
                'availability_days' => json_encode($provider['availabilityDays'] ?? json_decode($providerfind->availability_days, true)),
                'availability_hours' => json_encode([
                    'start' => $provider['availabilityHours']['start'] ?? json_decode($providerfind->availability_hours, true)['start'],
                    'end' => $provider['availabilityHours']['end'] ?? json_decode($providerfind->availability_hours, true)['end'],
                ]),
                'authorized_persons' => json_encode($provider['authorizedPerson'] ?? json_decode($providerfind->authorized_persons, true)),
                'profile_image' => $imagePath,
            ];
    
            $providerfind->update($providerData);
    
            return response()->json([
                'message' => 'Provider details updated successfully',
                'data' => $providerfind->refresh(), // Ensure fresh data from the database
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update provider',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        
    
    
    
    
}
