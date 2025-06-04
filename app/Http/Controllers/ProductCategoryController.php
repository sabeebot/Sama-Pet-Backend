<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;
use App\Helpers\FirebaseStorageHelper;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($provider_id)
    {

        try {
            $ProductCategories = ProductCategory::all();
    
            return response()->json($ProductCategories, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


        $ProductCategories = ProductCategory::where('provider_id', $provider_id)->get();
        return response()->json($ProductCategories);
    }


    /**
     * Store a new product category from the admin dashboard.
     * This method uses only the required fields: name, total_stock, selected_subcategory, and description.
     */
    public function storeProductCategoryAdminDashboard(Request $request)
    {
        Log::debug('Admin Dashboard Category Request:', $request->all());

        // Extract category and provider data
        $category = $request->input('category');
        $provider = $request->input('provider_data');

        if (!$provider || !isset($provider['profileId'])) {
            Log::debug('Provider data or profile ID is missing in admin request.');
            return response()->json(['error' => 'Provider data or profile ID is missing.'], 422);
        }

        $provider_id = $provider['profileId'];
        Log::debug('Admin Provider ID:', ['provider_id' => $provider_id]);

        // Validate only the required fields.
        $validator = Validator::make($category, [
            'name'                => 'required|string',
            'total_stock'         => 'required|integer',
            'selected_subcategory'=> 'required|string',  // pet type field
            'description'         => 'required|string'
        ]);

        if ($validator->fails()) {
            Log::debug('Admin category validation failed:', $validator->errors()->all());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Prepare data for insertion. The extra field (selected_category) is not needed.
        $tableData = [
            'name'                 => $category['name'],
            'total_stock'          => $category['total_stock'],
            'selected_subcategory' => $category['selected_subcategory'],
            'description'          => $category['description'],
            'price'                => '00',
            'availability'         => true,
            'total_sold'           => '00',
            'status'               => 'deactive',
            'provider_id'          => $provider_id,
            'selected_category'    => '',  // not used in admin dashboard
             'image_url'            => ''
        ];

        Log::debug('Storing admin dashboard category with data:', $tableData);

        $storedCategory = ProductCategory::create($tableData);
        Log::debug('Admin Dashboard Category stored successfully:', ['storedCategory' => $storedCategory]);

        return response()->json([
            'message'  => 'Admin dashboard category added successfully.',
            'category' => $storedCategory,
        ], 200);
    }

    public function getCategories(Request $request)
    {
        try {
            $categories = \App\Models\ProductCategory::all();
            Log::debug('Fetched product categories:', $categories->toArray());
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching product categories: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        // Extract category and provider_data from the request
        $category = $request->input('category');
        $provider = $request->input('provider_data'); // Fetch provider data
       
    
        // Check if provider_data is null
        if (!$provider || !isset($provider['profileId'])) {
            return response()->json(['error' => 'Provider data or profile ID is missing.'], 422);
        }
    
        // Extract provider_id safely
        $provider_id = $provider['profileId'];
    
        // Initialize imagePath
        $imagePath = "null";
    
        // Check if imageUrl is a base64 string
        if (isset($category['imageUrl']) && !empty($category['imageUrl'])) {
            // Check if the image is a base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $category['imageUrl'])) {
                // Handle base64 image upload
                $base64Image = $category['imageUrl'];
                $imagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'CategoryDocument', 'imageUrl_' . uniqid() . '.png');
            } else {
                // If it's not a base64 string, handle it as a file upload
                if ($request->hasFile('category.imageUrl')) {
                    $image = $request->file('category.imageUrl');
                    $imagePath = FirebaseStorageHelper::uploadFile($image, 'CategoryDocument');
                }
            }
        }
    
        // Validate the category data
        $validator = Validator::make($category, [
            'name' => 'required|string',
            'selected_subcategory' => 'required|string',
            'selected_category' => 'required|string',
            'description' => 'required|string',
            'total_stock' => 'required|integer',
            'imageUrl' => 'required' // You can keep this if you want to ensure imageUrl is provided
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // try {
            // Prepare the data for insertion
            $tableData = [
                'name' => $category['name'],
                'total_stock' => $category['total_stock'],
                'selected_category' => $category['selected_category'],
                'selected_subcategory' => $category['selected_subcategory'],
                'description' => $category['description'],
                'price' => '00',
                'availability' => true,
                'total_sold' => '00',
                'status'=>'deactive',
                'provider_id' => $provider_id,
                'image_url' => $imagePath,
            ];
    
            // Insert the data into the database
            $storedCategory = ProductCategory::create($tableData);
    
            return response()->json([
                'message' => 'Category is added successfully.',
                'categories' => $storedCategory,
            ], 200);
        // } catch (\Exception $e) {
        //     Log::error('Error submitting Category: ' . $e->getMessage());
        //     return response()->json(['error' => 'An error occurred while submitting Category'], 500);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = ProductCategory::findOrFail($id);
        return response()->json($product);
    }
    public function update_status($id){
        $ProductCategory = ProductCategory::find($id);
    
        if ($ProductCategory) {
           if($ProductCategory->status === 'deactive'){
            $status = 'active';
           }else{
            $status = 'deactive';
           }

        }else{
            return response()->json(['message' => 'Product Category info not found'], 404);
        }
           $ProductCategory->update(['status'=>$status]);
        return response()->json(['message' => 'Product Category status updated successfully'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = ProductCategory::findOrFail($id);
      return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = $request->data;
    
        // Find the category by ID
        $categoryFind = ProductCategory::find($id);
    
        if (!$categoryFind) {
            return response()->json(['message' => 'Category not found'], 404);
        }
    
        $imagePath = $categoryFind->image_url;
    
        // Handle image update
        if (isset($category['image']) && !empty($category['image'])) {
            FirebaseStorageHelper::deleteFile($categoryFind->image_url);
    
            // Check if the image is a base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $category['image'])) {
                $base64Image = $category['image'];
                $imagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'CategoryDocument', 'imageUrl_' . uniqid() . '.png');
            } else if ($request->hasFile('data.image')) {
                // Handle as a file upload
                $image = $request->file('data.image');
                $imagePath = FirebaseStorageHelper::uploadFile($image, 'CategoryDocument');
            }
        }
    
        // Prepare data for update, retaining existing values if fields are missing
        $tableData = [
            'name' => $category['name'] ?? $categoryFind->name,
            'total_stock' => $category['total_stock'] ?? $categoryFind->total_stock,
            'selected_category' => $category['selected_category'] ?? $categoryFind->selected_category,
            'selected_subcategory' => $category['selected_subcategory'] ?? $categoryFind->selected_subcategory,
            'description' => $category['description'] ?? $categoryFind->description,
            'price' => $category['price'] ?? $categoryFind->price,
            'availability' => isset($category['availability']) ? $category['availability'] : $categoryFind->availability,
            'total_sold' => $category['total_sold'] ?? $categoryFind->total_sold,
            'provider_id' => $category['provider_id'] ?? $categoryFind->provider_id,
            'image_url' => $imagePath,
        ];
    
        // Update the category with the new data
        $categoryUpdated = $categoryFind->update($tableData);
    
        if ($categoryUpdated) {
            return response()->json(['message' => 'Category updated successfully', 'data' => $categoryFind], 200);
        } else {
            return response()->json(['message' => 'Failed to update Category'], 500);
        }
    }
    
    public function deleteAllByProvider($providerId)
{
    try {
        // Fetch all services for the given providerId
        $categories = ProductCategory::where('provider_id', $providerId)->get();

        // Check if services exist for the provider
        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No Category found for this provider.'], 404);
        }

        // Iterate through each service and delete
        foreach ($categories as $category) {
            // If the service has an image, delete it from Firebase
            if ($category->image_url && $category->image_url != "null") {
                try {
                    FirebaseStorageHelper::deleteFile($category->image_url);
                } catch (\Exception $e) {
                    Log::error('Error deleting service image: ' . $e->getMessage());
                }
            }
          // Delete the service from the database
            $category->delete();
        }

        // Return success message
        return response()->json(['message' => 'All Categories for the provider have been deleted successfully.'], 200);

    } catch (\Exception $e) {
        // Handle any error that occurs during the deletion process
        return response()->json(['message' => 'Error deleting Categories: ' . $e->getMessage()], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($category)
    {

        // Find the category
        $categoryFind = ProductCategory::find($category);
        if (!$categoryFind) {
            return response()->json(['message' => 'Product Category not found'], 404);
        }else{
            FirebaseStorageHelper::deleteFile($categoryFind->image_url);
            if ($categoryFind->delete()) {
                return response()->json(['message' => 'Product Category deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'Failed to delete Product Category'], 500);
            }
        }
   
       
    }
    
}