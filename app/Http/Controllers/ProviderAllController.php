<?php

namespace App\Http\Controllers;
use App\Models\Gallery;
use App\Models\Product;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;
use App\Models\Provider; // <-- Add this line

class ProviderAllController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    return 'ok';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function addCategory(Request $request)
    {
        return 'hello' ;
    $category = $request->tableData[0];
 
try{
           // $validatedData = $request->validate([
    //     'name' => 'required|string|max:255',
    //     'totalStock' => 'required|integer',
    //     'selected_category' => 'required|string|max:255',
    //     'selected_subcategory' => 'required|string|max:255',
    //     'description' => 'required|string',
    //     'price' => 'nullable|numeric',
    //     'availability' => 'nullable|boolean',
    //     'total_sold' => 'nullable|integer',
    //     'imageUrl' => 'required|string|max:255',
    // ]);
    $tableData = [
        'name' =>  $category->name,
        'total_stock' =>  $category->totalStock,
        'selected_category' => 'Null',
        'selected_subcategory' => 'Null',
        'description' => $category->description,
        'price' => '00',
        'availability' => true,
        'total_sold' => '00',
        'image_url' =>  $category->imageUrl,
    ];
 ProductCategory::create($tableData);
    return response()->json([
        'message' => 'Category is added successfully.',
        // 'provider' => $provider
    ], 200);

} catch (\Exception $e) {
    Log::error('Error submitting Category : ' . $e->getMessage());
    return response()->json(['error' => 'An error occurred while submitting Category'], 500);
}

    // Validate the data

    // Create and store the data

    }

public function getCategory(){
    try{
   $category = ProductCategory::get();
   return response()->json([
    'message' => 'Category is added successfully.',
    'category' => $category
], 200);

} catch (\Exception $e) {
Log::error('Error submitting Category : ' . $e->getMessage());
return response()->json(['error' => 'An error occurred while submitting Category'], 500);
}

}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return 'okkk';
        // return $request ;
        // $validated = $request->validate([
        //     'type' => 'required|in:doctor,pet shop,groomer,pet clinic,trainer',
        //     'name' => 'required|string|max:64|unique:providers',
        //     'email' => 'required|email|max:64|unique:providers',
        //     'office' => 'required|string|max:128',
        //     'road' => 'required|string|max:128',
        //     'block' => 'required|string|max:128',
        //     'city' => 'required|string|max:128',
        //     'contact_no' => 'required|string|max:64',
        //     'provider_name_en' => 'required|string|max:128',
        //     'provider_name_ar' => 'required|string|max:128',
        //     'start_date' => 'nullable|string',
        //     'end_date' => 'nullable|string',
        //     'cr_number' => 'required|string|max:64',
        //     'website' => 'nullable|string',
        //     'instagram' => 'nullable|string',
        //     'availability_days' => 'nullable|array',
        //     'availability_hours' => 'nullable|array',
        //     'authorized_person' => 'nullable|array',
        // ]);

        // if ($validated->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        // try {
         // Create a new provider using the validated data
    // $provider = Provider::create([
    //     'type' => $request->providerType ?? '',
    //     'name' => $request->businessName,
    //     'contact_no' => $request->phoneNumber,
    //     'email' => $request->companyEmail,
    //     'provider_name_en' => $request->providerNameEn,
    //     'provider_name_ar' => $request->providerNameAr,
    //     'cr_number' => $request->crNumber,
    //     'instagram' => $request->instagramUrl,
    //     'office' => $request->office ?? '',
    //     'road' => $request->road ?? '',
    //     'block' => $request->block ?? '',
    //     'city' => $request->city ?? '',
    //     'password' => 'test',
    //     'social_media' => 'test',
    //     'documents'=>'test',
    //     // 'address' => $request->filled('location') ? $request->location : implode(', ', array_filter([
    //     //     $request->buildingNo,
    //     //     $request->road,
    //     //     $request->block,
    //     //     $request->area
    //     // ])),
    //     'status' => 'pending',
    //     'availability_days' => json_encode($request->availabilityDays ?? []),
    //     'availability_hours' => json_encode([
    //         'start' => $request->availabilityStart ?? '',
    //         'end' => $request->availabilityEnd ?? ''
    //     ]),
    //     'authorized_person_name' => $request->authorizedPerson['name'] ?? '',
    //     'authorized_person_position' => $request->authorizedPerson['position'] ?? '',
    //     'authorized_person_contact_nmuber' => $request->authorizedPerson['contactNumber'] ?? '',
    //     'authorized_person_email' => $request->authorizedPerson['email'] ?? '',
    // ]);




    // $request->validate([
    //     'title' => 'required|string|max:128',
    //     'short_description' => 'required|string|max:256',
    //     'old_price' => 'required|numeric',
    //     'new_price' => 'nullable|numeric',
    //     'percentage' => 'nullable|integer',
    //     'image' => 'nullable|string|max:128',
    //     'contact_number' => 'required|string',
    //     'pet_type' => 'required|array',
    //     'provider_id' => 'required|exists:providers,id',
    //     'short_description_ar' => 'required|string|max:256',
    //     'title_ar' => 'required|string|max:128',
    //     'status' => 'nullable|string|max:64',
    //     'discount' => 'nullable|numeric',
    //     'service_description_ar' => 'nullable|string|max:256',
    //     'service_description_en' => 'nullable|string|max:256',
    //     'service_name_ara' => 'nullable|string|max:128',
    //     'service_name_eng' => 'nullable|string|max:128',
    // ]);

    // Store the data
    // $service = Service::create([
    //     'title' => $request->title,
    //     'short_description' => $request->short_description,
    //     'old_price' => $request->old_price,
    //     'new_price' => $request->new_price,
    //     'percentage' => $request->percentage,
    //     'image' => $request->image,
    //     'contact_number' => $request->contact_number,
    //     'pet_type' => $request->pet_type,
    //     'provider_id' => $request->provider_id,
    //     'short_description_ar' => $request->short_description_ar,
    //     'title_ar' => $request->title_ar,
    //     'status' => $request->status ?? 'active',
    //     'discount' => $request->discount ?? 0,
    //     'service_description_ar' => $request->service_description_ar,
    //     'service_description_en' => $request->service_description_en,
    //     'service_name_ara' => $request->service_name_ara,
    //     'service_name_eng' => $request->service_name_eng,
    // ]);

    // $tableData = [
    //     'name' => 'Electronics',
    //     'total_stock' => 100,
    //     'selected_category' => 'Gadgets',
    //     'selected_subcategory' => 'Smartphones',
    //     'description' => 'Latest smartphones with advanced features.',
    //     'price' => 699.99,
    //     'availability' => true,
    //     'total_sold' => 50,
    //     'image_url' => 'images/smartphones.jpg',
    // ];

    // Validate the data
    // $validatedData = $request->validate([
    //     'name' => 'required|string|max:255',
    //     'total_stock' => 'required|integer',
    //     'selected_category' => 'required|string|max:255',
    //     'selected_subcategory' => 'required|string|max:255',
    //     'description' => 'required|string',
    //     'price' => 'nullable|numeric',
    //     'availability' => 'nullable|boolean',
    //     'total_sold' => 'nullable|integer',
    //     'image_url' => 'required|string|max:255',
    // ]);

    // Create and store the data
    // $productCategory = ProductCategory::create($tableData);
            
    // return response()->json([
    //     'message' => 'Provider information submitted successfully. Your application is pending approval.',
    //     // 'provider' => $provider
    // ], 200);


        // Validate the incoming data
        $validatedData = $request->validate([
            'productDetails.amount' => 'required|integer',
            'productDetails.discount' => 'nullable|integer',
            'productDetails.imageUrl' => 'nullable|string|max:255',
            'productDetails.priceAfter' => 'required|numeric',
            'productDetails.priceBefore' => 'required|numeric',
            'productDetails.productDescriptionAr' => 'required|string|max:255',
            'productDetails.productDescriptionEn' => 'required|string|max:255',
            'productDetails.productNameEn' => 'required|string|max:128',
            'productDetails.sCategory' => 'required|integer|exists:product_categories,id',
            'productDetails.typeOfPet' => 'required|array',
            'productDetails.quantity' => 'required|integer',
            'productDetails.contactNumber' => 'required|string|max:20',
            'productDetails.providerId' => 'required|exists:providers,id',
            'productDetails.percentage' => 'nullable|integer',
        ]);
    
        // Extract data from the request
        $productDetails = $request->productDetails;
    
        // Create and store the product data
        $product = Product::create([
            'name' => $productDetails['productNameEn'],
            'old_price' => $productDetails['priceBefore'],
            'new_price' => $productDetails['priceAfter'],
            'quantity' => $productDetails['quantity'],
            'percentage' => $productDetails['percentage'] ?? null,
            'contact_number' => $productDetails['contactNumber'],
            'pet_type' => json_encode($productDetails['typeOfPet']),
            'provider_id' => $productDetails['providerId'],
            'category_id' => $productDetails['sCategory'],
            'product_name_en' => $productDetails['productNameEn'],
            'product_description_en' => $productDetails['productDescriptionEn'],
            'product_description_ar' => $productDetails['productDescriptionAr'],
            'amount' => $productDetails['amount'],
            'discount' => $productDetails['discount'] ?? 0,
            'image_url' => $productDetails['imageUrl'],
        ]);


    // Validate the incoming data
    $request->validate([
        'provider_id' => 'required|exists:providers,id',
        'gallaryDeail.typeEN' => 'required|string|max:128',
        'gallaryDeail.imagesUrl' => 'required|string|max:255',
    ]);

    // Prepare the data for insertion
    $data = [
        'provider_id' => $request->provider_id,
        'type_en' => $request->gallaryDeail['typeEN'] ?? null,
        'type_ar' => $request->gallaryDeail['typeAR'] ?? null,
        'description_en' => $request->gallaryDeail['descriptionEN'] ?? null,
        'description_ar' => $request->gallaryDeail['descriptionAR'] ?? null,
        'image_url' => $request->gallaryDeail['imagesUrl'] ?? null,
    ];

    // Use the create method to insert data
    Gallery::create($data);

            return response()->json([
                'message' => 'Provider information submitted successfully. Your application is pending approval.',
                // 'provider' => $provider
            ], 200);

        // } catch (\Exception $e) {
        //     Log::error('Error submitting provider information: ' . $e->getMessage());
        //     return response()->json(['error' => 'An error occurred while submitting provider information'], 500);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $providers = Provider::with([
            'services',      // Load related services
            'products',      // Load related products
            'products_catgory', // Load related product categories
            'Gallery',       // Load related gallery
            // 'veterinarians', // Load related veterinarians
            'doctorInfo',    // Load related doctor info
            // 'trainerInfo'    // Load related trainer info
        ])->get();
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
