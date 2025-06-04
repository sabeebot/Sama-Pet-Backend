<?php

namespace App\Http\Controllers;

use App\Models\DoctorInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\FirebaseStorageHelper; 
use Illuminate\Support\Facades\Log;


class DoctorInfoController extends Controller
{
    /**
     * Display a listing of all doctor info.
     */
    public function index()
    {
        $doctorInfo = DoctorInfo::all();
        return response()->json($doctorInfo, 200);
    }

    /**
     * Display the specified doctor info by ID.
     */
    public function show($id)
    {
        $doctorInfo = DoctorInfo::find($id);
    
        if (!$doctorInfo) {
            return response()->json(['message' => 'Doctor info not found'], 404);
        }
    
        return response()->json(['doctor' => $doctorInfo], 200);
    }
    

    public function store(Request $request)
    {
    
    
        // Validation
        $validator = Validator::make($request->all(), [
            'DoctorData.nameEng' => 'nullable|string',
            // 'DoctorData.nameAra' => 'required|string',
            'DoctorData.educationEng' => 'nullable|string',
            // 'DoctorData.educationAra' => 'required|string',
            'DoctorData.noOfYearEng' => 'nullable|integer',
            // 'DoctorData.noOfYearAra' => 'required|integer',
            'DoctorData.contantEng' => 'nullable|string',
            // 'DoctorData.contentAra' => 'required|string',
            'DoctorData.availbiltyDay' => 'nullable|string',
            'DoctorData.filterDate' => 'nullable|string',
            'DoctorData.filterTime' => 'nullable|string',
            'DoctorData.introEng' => 'nullable|string',
            // 'DoctorData.introAra' => 'nullable|string',
            'DoctorData.profileId' => 'required|integer',
            'DoctorData.imageUrl' => 'nullable',  // Changed to string to handle base64 image
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Profile ID ko DoctorData se lein
        $doctorData = $request->DoctorData;
        $profileId = $doctorData['profileId'];
    

  // Handle image upload if exists
$imagePath = null; // Default null if no image provided

// Check if 'DoctorData.imageUrl' exists and is not empty
if (isset($request->DoctorData['imageUrl']) && !empty($request->DoctorData['imageUrl'])) {
    $base64Image = $request->DoctorData['imageUrl'];

        // Apply uploadBase64Image if it's a base64 string
        $imagePath = FirebaseStorageHelper::uploadBase64Image(
            $base64Image,
            'doctorImages',
            'doctor_image_' . uniqid() . '.png'
        );
    
} elseif (isset($request->DoctorData['imageUrl']) && $request->hasFile('DoctorData.imageUrl')) {
    // Apply uploadFile if it's a regular file
    $image = $request->file('DoctorData.imageUrl');
    $imagePath = FirebaseStorageHelper::uploadFile($image, 'doctorImages');
}
        // Doctor info array
        $doctorInfo = [
            'provider_id' => $profileId,
            'years_of_experience' => $doctorData['noOfYearEng'],
            'medical_degree_and_specializtion' => $doctorData['educationEng'],
            'availbiltyDay' => $doctorData['availbiltyDay'],
            'contantEng' => $doctorData['contantEng'],
            // 'contentAra' => $doctorData['contentAra'],
            'educationEng' => $doctorData['educationEng'],
            // 'educationAra' => $doctorData['educationAra'],
            'filterDate' => $doctorData['filterDate'],
            'filterTime' => $doctorData['filterTime'],
            'imageUrl' => $imagePath,  // Store the image URL or file path from Firebase
            // 'introAra' => $doctorData['introAra'],
            'introEng' => $doctorData['introEng'],
            // 'nameAra' => $doctorData['nameAra'],
            'nameEng' => $doctorData['nameEng'],
            'noOfYearAra' => $doctorData['noOfYearAra'],
            'noOfYearEng' => $doctorData['noOfYearEng'],
            'status' =>'deactive',
        ];
    
        // Store in database
        $storeDoctor = DoctorInfo::create($doctorInfo);
    
        if ($storeDoctor) {
            return response()->json([
                'message' => 'Doctor data stored successfully!',
                'data' => $storeDoctor,
            ], 201);
        } else {
            return response()->json([
                'message' => 'An error occurred while storing doctor data',
            ], 500);
        }
    }
    
    
    public function update_status($id){
        
        $doctorInfo = DoctorInfo::find($id);
    
        if ($doctorInfo) {
           if($doctorInfo->status === 'deactive'){
            $status = 'active';
           }else{
            $status = 'deactive';
           }

        }else{
            return response()->json(['message' => 'Doctor info not found'], 404);
        }
           $doctorInfo->update(['status'=>$status]);
        return response()->json(['message' => 'Doctor status updated successfully'], 200);
    }

    public function update_doctor(Request $request, $doctorid, $providerid)
    {
        $doctorData = $request->data;
    
        $doctor = DoctorInfo::find($doctorid);
    
        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found!',
            ], 404);
        }
    
        // Handle image updates
        $imagePath = $doctor->imageUrl; // Default to the existing image URL
        if (!empty($doctorData['image'])) {
            if (is_string($doctorData['image'])) {
                // Handle base64 image upload
                $imagePath = FirebaseStorageHelper::uploadBase64Image(
                    $doctorData['image'],
                    'doctorImages',
                    'doctor_image_' . uniqid() . '.png'
                );
            } elseif ($request->hasFile('data.image')) {
                // Handle regular file upload
                $image = $request->file('data.image');
                $imagePath = FirebaseStorageHelper::uploadFile($image, 'doctorImages');
            }
        }
    
        // Prepare the updated data by checking each field
        $doctorInfo = [
            'provider_id' => $providerid,
            'years_of_experience' => $doctorData['noOfYearEng'] ?? $doctor->years_of_experience,
            'medical_degree_and_specializtion' => $doctorData['educationEng'] ?? $doctor->medical_degree_and_specializtion,
            'availbiltyDay' => $doctorData['availbiltyDay'] ?? $doctor->availbiltyDay,
            'contantEng' => $doctorData['contantEng'] ?? $doctor->contantEng,
            // 'contentAra' => $doctorData['contentAra'] ?? $doctor->contentAra,
            'educationEng' => $doctorData['educationEng'] ?? $doctor->educationEng,
            // 'educationAra' => $doctorData['educationAra'] ?? $doctor->educationAra,
            'filterDate' => $doctorData['filterDate'] ?? $doctor->filterDate,
            'filterTime' => $doctorData['filterTime'] ?? $doctor->filterTime,
            'imageUrl' => $imagePath, // Update the image URL
            // 'introAra' => $doctorData['introAra'] ?? $doctor->introAra,
            'introEng' => $doctorData['introEng'] ?? $doctor->introEng,
            // 'nameAra' => $doctorData['nameAra'] ?? $doctor->nameAra,
            'nameEng' => $doctorData['nameEng'] ?? $doctor->nameEng,
            // 'noOfYearAra' => $doctorData['noOfYearAra'] ?? $doctor->noOfYearAra,
            'noOfYearEng' => $doctorData['noOfYearEng'] ?? $doctor->noOfYearEng,
            'status' => $doctorData['status'] ?? $doctor->status,
        ];
    
        // Update the doctor record
        $doctor->update($doctorInfo);
    
        // Return the updated doctor data in the response
        return response()->json([
            'message' => 'Doctor data updated successfully!',
            'doctor' => $doctor, // Return the updated doctor data
        ], 200);
    }
    public function deleteAllDoctorByProvider($provider_id)
    {
        
        try {
            // Retrieve all products for the given provider ID
            $doctors = DoctorInfo::where('provider_id', $provider_id)->get();
    
            // Check if products exist for the provider
            if ($doctors->isEmpty()) {
                return response()->json(['message' => 'No doctor found for this provider.'], 404);
            }
    
            // Delete each product and its associated image
            foreach ($doctors as $doctor) {
                if ($doctor->imageUrl && $doctor->imageUrl!='null') {
                    FirebaseStorageHelper::deleteFile($doctor->imageUrl); // Delete the product image
                }
                $doctor->delete(); // Delete the product
            }
    
            return response()->json(['message' => 'All doctors for the provider have been deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error deleting doctors for provider: ' . $e->getMessage());
    
            return response()->json(['error' => 'An error occurred while deleting doctors.'], 500);
        }
    }
      /**
     * Display a listing of doctor info by provider ID.
     */
    public function destroy(string $id) {
        try {
            $doctor = DoctorInfo::find($id);
    
            if (!$doctor) {
                return response()->json(['error' => 'Doctor record not found.'], 404);
            }
    
            if ($doctor->imageUrl && $doctor->imageUrl != 'null') {
                FirebaseStorageHelper::deleteFile($doctor->imageUrl); // Delete the associated image
            }
    
            $doctor->delete();
    
            return response()->json(['message' => 'Doctor record deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete doctor record: ' . $e->getMessage()], 500);
        }
    }
    
    public function getDoctorInfoByProviderId($provider_id)
    {
        
        $doctorInfo = DoctorInfo::where('provider_id', $provider_id)->get();

        if ($doctorInfo->isEmpty()) {
            return response()->json(['message' => 'No doctor info found for this provider'], 404);
        }

        return response()->json($doctorInfo, 200);
    }
}
