<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\FirebaseStorageHelper;
use App\Http\Requests\AddProductRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    public function index()
    {
        try {
            // Retrieve all products from the database
            $products = Product::all();    
    
            // Return the products as a JSON response with a 200 status code
            return response()->json($products, 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes (optional)
            Log::error('Error fetching products: ' . $e->getMessage());
    
            // Return a JSON response with the error message and a 500 status code
            return response()->json(['error' => 'An error occurred while fetching products.'], 500);
        }
    }

    public function samaStoreIndex()
{
    try {
        $products = \App\Models\Product::all();
        // Return a collection of products as ProductResource
        return \App\Http\Resources\ProductResource::collection($products);
    } catch (\Exception $e) {
        Log::error('Error fetching products: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function updateProductAdminDashboard(Request $request, $id)
{
    Log::debug('Admin Dashboard Product Update Request:', $request->all());

    $product = Product::findOrFail($id);
    $input = $request->input('product');
    $provider = $request->input('provider_data');

    if (!$provider || !isset($provider['profileId'])) {
        Log::debug('Provider data or profile ID is missing in admin product update request.');
        return response()->json(['error' => 'Provider data or profile ID is missing.'], 422);
    }

    // Validate required fields.
    $validator = Validator::make($input, [
        'pet_type'            => 'required|string',
        'category_id'            => 'nullable|integer',
        'product_name_en'        => 'required|string',
        'product_name_ar'        => 'required|string',
        'product_description_en' => 'required|string',
        'product_description_ar' => 'required|string',
        'quantity'               => 'required|integer',
        'price_before'           => 'required|numeric',
        'price_after'            => 'required|numeric',
        'discount'               => 'required|numeric'
    ]);

    if ($validator->fails()) {
        Log::debug('Admin product update validation failed:', $validator->errors()->all());
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Prepare data for update.
    $data = [
        'pet_type'            => $input['pet_type'],
        'category_id'            => $input['category_id'] ?? null,
        'product_name_en'        => $input['product_name_en'],
        'product_name_ar'        => $input['product_name_ar'],
        'product_description_en' => $input['product_description_en'],
        'product_description_ar' => $input['product_description_ar'],
        'quantity'               => $input['quantity'],
        'price_before'           => $input['price_before'],
        'price'                  => $input['price_after'],
        'discount'               => $input['discount']
    ];

    Log::debug('Updating admin product with data:', $data);
    $product->update($data);
    Log::debug('Admin Product updated successfully:', ['product' => $product]);

    return response()->json([
        'message' => 'Admin product updated successfully',
        'product' => $product,
    ], 200);
}






    /**
     * Store a new product from the admin dashboard.
     */
    public function storeProductAdminDashboard(Request $request)
{
    Log::debug('Admin Dashboard Product Request:', $request->all());

    // Extract product data and provider data.
    $product = $request->input('product');
    $provider = $request->input('provider_data');

    if (!$provider || !isset($provider['profileId'])) {
        Log::debug('Provider data or profile ID is missing in admin product request.');
        return response()->json(['error' => 'Provider data or profile ID is missing.'], 422);
    }

    $provider_id = $provider['profileId'];
    Log::debug('Admin Provider ID:', ['provider_id' => $provider_id]);

    // Validate required fields.
    $validator = Validator::make($product, [
        'pet_type'            => 'required|string',
        'category_id'            => 'nullable|integer',
        'product_name_en'        => 'required|string',
        'product_name_ar'        => 'required|string',
        'product_description_en' => 'required|string',
        'product_description_ar' => 'required|string',
        'quantity'               => 'required|integer',
        'price_before'           => 'required|numeric',
        'price_after'            => 'required|numeric',  // Value from input "price-after"
        'discount'               => 'required|numeric'
    ]);

    if ($validator->fails()) {
        Log::debug('Admin product validation failed:', $validator->errors()->all());
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Prepare data for insertion.
    $data = [
        'pet_type'            => $product['pet_type'],
        'category_id'            => $product['category_id'] ?? null,
        'product_name_en'        => $product['product_name_en'],
        'product_name_ar'        => $product['product_name_ar'],
        'product_description_en' => $product['product_description_en'],
        'product_description_ar' => $product['product_description_ar'],
        'quantity'               => $product['quantity'],
        'price_before'           => $product['price_before'],
        'price'                  => $product['price_after'], // Use value from "price-after" input
        'discount'               => $product['discount'],
        'provider_id'            => $provider_id,
        'status'                 => '0'
    ];

    Log::debug('Storing admin product with data:', $data);
    $storedProduct = Product::create($data);
    Log::debug('Admin Product stored successfully:', ['storedProduct' => $storedProduct]);

    return response()->json([
        'message' => 'Admin product added successfully',
        'product' => $storedProduct,
    ], 200);
}


public function deleteProductAdminDashboard(Request $request, $id)
{
    Log::debug('Admin Dashboard Product Delete Request:', $request->all());

    $provider = $request->input('provider_data');
    if (!$provider || !isset($provider['profileId'])) {
        Log::debug('Provider data or profile ID is missing in admin product delete request.');
        return response()->json(['error' => 'Provider data or profile ID is missing.'], 422);
    }

    $product = Product::find($id);
    if (!$product) {
        return response()->json(['error' => 'Product not found.'], 404);
    }

    $product->delete();
    Log::debug('Admin Product deleted successfully:', ['product_id' => $id]);

    return response()->json(['message' => 'Admin product deleted successfully'], 200);
}
   
  



   /**
 * Store a newly created product.
 */
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_name_en' => 'required|string|max:255',
        'product_name_ar' => 'required|string|max:255',
        'pet_type'        => 'required|string|max:50',
        'status'          => 'required|integer|in:0,1',
        'quantity'        => 'required|integer|min:1',
        'start_date'      => 'required|date',
        'received_date'   => 'required|date',
        'price'           => 'required|numeric|min:0',
        'delivery_price'  => 'required|numeric|min:0',
        'imageUrl'        => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // Set default image URL (if desired)
    $imagePath = 'default.png';

    // Process image if provided as a Base64 string
    if (!empty($data['imageUrl']) && preg_match('/^data:image\/(\w+);base64,/', $data['imageUrl'], $matches)) {
        $extension = strtolower($matches[1]); // jpg, jpeg, or png
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return response()->json(['error' => 'Invalid image type. Only JPG, JPEG, and PNG are allowed.'], 422);
        }
        $imageData = substr($data['imageUrl'], strpos($data['imageUrl'], ',') + 1);
        $decodedImage = base64_decode($imageData);
        if ($decodedImage === false) {
            return response()->json(['error' => 'Base64 decoding failed.'], 422);
        }
        $fileName = uniqid() . '.' . $extension;
        $firebasePath = 'productDocuments/' . $fileName;

        $serviceAccountPath = storage_path('app/firebase-auth.json');
        if (!file_exists($serviceAccountPath)) {
            return response()->json(['error' => 'Firebase service account file not found.'], 500);
        }

        try {
            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();

            $bucket->upload($decodedImage, [
                'name' => $firebasePath,
                'predefinedAcl' => 'publicRead'
            ]);

            $imagePath = "https://storage.googleapis.com/{$bucket->name()}/{$firebasePath}";
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image upload failed: ' . $e->getMessage()], 500);
        }
    }

    // Merge the image path into data and create product.
    $data['image_url'] = $imagePath;
    // Create the product (assuming your Product model's $fillable includes image_url)
    $product = Product::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Product stored successfully.'
    ]);
}



   public function show($id)
{
    // Retrieve all products for the given provider ID
    $products = Product::where('provider_id', $id)->get();

    // Check if products are found
    if ($products->isEmpty()) {
        return response()->json(['message' => 'No products found for this provider.'], 404);
    }

    // Return the products as a JSON response
    return response()->json($products);
}

    public function getMostOrderedProducts($provider_id)
    {        
        $mostOrderedProducts = Product::whereHas('provider', function ($query) use ($provider_id) {
            $query->where('id', $provider_id);
        })
        ->withCount('orders')
        ->orderBy('orders_count', 'desc')
        ->take(5) // Get top 5 most ordered products
        ->get();

        return response()->json($mostOrderedProducts);
    }

    public function getProductsByProvider($provider_id)
    {
        $products = Product::where('provider_id', $provider_id)->get();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found'], 404);
        }

    }


    public function getByProvider($providerId)
{
    $products = Product::where('provider_id', $providerId)->get();

    return response()->json($products);
}


  

    public function addProduct(Request $request, $provider_id) {



        $imagePath = 'null';
        
        
        if ($request->has('imageUrl') && !empty($request->input('imageUrl'))) {
            $base64Image = $request->input('imageUrl');
            // Apply uploadBase64Image if it's a base64 string
            $imagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'productDocuments', 'imageUrl' . uniqid() . '.png');
        } elseif ($request->hasFile('imageUrl')) {
            // Apply uploadFile if it's a regular file
            $image = $request->file('imageUrl');
            $imagePath = FirebaseStorageHelper::uploadFile($image, 'productDocuments');
        }
        
        
       

        if ($this->isJson($request->newProduct)) {
            $validatedData = json_decode($request->newProduct, true); // Decode JSON into an associative array
        } else {
            $validatedData = $request->newProduct; // Use the data as is
        }    
        // Validate the product details
        $validator = Validator::make($validatedData, [
            'sCategory' => 'required',
            'productNameEn' => 'required|string',
            'productDescriptionEn' => 'required|string',
            'productDescriptionAr' => 'required|string',
            'discount' => 'required|numeric',
            'priceAfter' => 'required|numeric',
            'amount' => 'required|numeric',
            'typeOfPet' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        
        // Prepare product data
        $product = [
            'provider_id' => $provider_id,
            'category_id' => $validatedData['sCategory'],
            'name' => 'null',
            'product_name_en' => $validatedData['productNameEn'],
            'product_description_en' => $validatedData['productDescriptionEn'],
            'product_description_ar' => $validatedData['productDescriptionAr'],
            'amount' => 000,
            'discount' => $validatedData['discount'],
            'old_price' => $validatedData['priceBefore'],
            'new_price' => $validatedData['priceAfter'],
            'quantity' => $validatedData['amount'],
            'image_url' => $imagePath,
            'description' => 'null',
            'contact_number' => 'null',
            'status'=>'deactive',
            'pet_type' => $validatedData['typeOfPet'],
        ];
    
        $productStore = Product::create($product);
    
        if ($productStore) {
            return response()->json(['message' => 'Product added successfully', 'data' => $product], 201);
        } else {
            return response()->json(['message' => 'Failed to add product'], 500);
        }
    }

    private function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
    
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }




    
    public function deleteAllProductsByProvider($provider_id)
    {
        
        try {
            // Retrieve all products for the given provider ID
            $products = Product::where('provider_id', $provider_id)->get();
    
            // Check if products exist for the provider
            if ($products->isEmpty()) {
                return response()->json(['message' => 'No products found for this provider.'], 404);
            }
    
            // Delete each product and its associated image
            foreach ($products as $product) {
                if ($product->image_url && $product->image_url!='null') {
                    FirebaseStorageHelper::deleteFile($product->image_url); // Delete the product image
                }
                $product->delete(); // Delete the product
            }
    
            return response()->json(['message' => 'All products for the provider have been deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error deleting products for provider: ' . $e->getMessage());
    
            return response()->json(['error' => 'An error occurred while deleting products.'], 500);
        }
    }
    
    public function update_status($id){
        $Product = Product::find($id);
    
        if ($Product) {
           if($Product->status === 'deactive'){
            $status = 'active';
           }else{
            $status = 'deactive';
           }

        }else{
            return response()->json(['message' => 'Product info not found'], 404);
        }
           $Product->update(['status'=>$status]);
        return response()->json(['message' => 'Product status updated successfully'], 200);
    }
    
    public function deleteProduct($provider_id, $id) {
        // Find the product by ID and provider_id
        $product = Product::where('id', $id)
                          ->where('provider_id', $provider_id)
                          ->first();
      FirebaseStorageHelper::deleteFile($product->image_url);
        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }    
        // Attempt to delete the product
        if ($product->delete()) {
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete product'], 500);
        }
    }


    public function getProductById($id)
{
    try {
        $product = Product::findOrFail($id);
        Log::debug('Product fetched: ', $product->toArray());
        return response()->json($product);
    } catch (ModelNotFoundException $e) {
        Log::error('Product not found: ' . $id);
        return response()->json(['message' => 'Product not found'], 404);
    } catch (\Exception $e) {
        Log::error('Failed to fetch product: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to fetch product', 'error' => $e->getMessage()], 500);
    }
}

    public function updateProduct(Request $request, $provider_id, $product_id)
    {
        $product = Product::find($product_id);
    
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        // Retain existing image URL by default
        $imagePath = $product->image_url;
    
        // Handle image upload
        if ($request->has('data.image') && !empty($request->input('data.image'))) {
            if ($product->image_url) {
                FirebaseStorageHelper::deleteFile($product->image_url);
            }
            $base64Image = $request->input('data.image');
            $imagePath = FirebaseStorageHelper::uploadBase64Image($base64Image, 'productDocuments', 'image_' . uniqid() . '.png');
        } elseif ($request->hasFile('data.image')) {
            if ($product->image_url) {
                FirebaseStorageHelper::deleteFile($product->image_url);
            }
            $image = $request->file('data.image');
            $imagePath = FirebaseStorageHelper::uploadFile($image, 'productDocuments');
        }
    
        $validatedData = $request->data;
    
        // Validation rules for the product
        $validator = Validator::make($validatedData, [
            'sCategory' => 'sometimes|required',
            'productNameEn' => 'sometimes|required|string',
            'productDescriptionEn' => 'sometimes|required|string',
            'productDescriptionAr' => 'sometimes|required|string',
            'discount' => 'sometimes|required|numeric',
            'priceAfter' => 'sometimes|required|numeric',
            'amount' => 'sometimes|required|numeric',
            'typeOfPet' => 'sometimes|required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Merge existing data with new input, retaining existing values where fields are missing
        $productToStore = [
            'provider_id' => $provider_id,
            'category_id' => $validatedData['sCategory'] ?? $product->category_id,
            'name' => $product->name ?? 'null',
            'product_name_en' => $validatedData['productNameEn'] ?? $product->product_name_en,
            'product_description_en' => $validatedData['productDescriptionEn'] ?? $product->product_description_en,
            'product_description_ar' => $validatedData['productDescriptionAr'] ?? $product->product_description_ar,
            'amount' => $validatedData['amount'] ?? $product->amount,
            'discount' => $validatedData['discount'] ?? $product->discount,
            'old_price' => $validatedData['priceBefore'] ?? $product->old_price,
            'new_price' => $validatedData['priceAfter'] ?? $product->new_price,
            'quantity' => $validatedData['amount'] ?? $product->quantity,
            'image_url' => $imagePath,
            'description' => $product->description ?? 'null',
            'contact_number' => $product->contact_number ?? 'null',
            'pet_type' => $validatedData['typeOfPet'] ?? $product->pet_type,
        ];
    
        // Update the product
        $updated = $product->update($productToStore);
    
        if ($updated) {
            return response()->json(['message' => 'Product updated successfully', 'data' => $product], 200);
        } else {
            return response()->json(['message' => 'Failed to update product'], 500);
        }
    }
    
}
