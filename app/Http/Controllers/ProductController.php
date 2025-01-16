<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\FirebaseStorageHelper;
use App\Http\Requests\AddProductRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateProductRequest;

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
            'discount' => 'required|integer',
            'priceAfter' => 'required|integer',
            'amount' => 'required|integer',
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


    public function getProductbyId($provider_id,$product_id) {
       
        // Find the product by ID and provider_id
        $product = Product::where('id', $product_id)
                          ->where('provider_id', $provider_id)
                          ->first();
                                 
        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }else{
             //return the updated product as a response
            return response()->json(['message' => 'Product Get successfully', 'data' => $product], 200);
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
            'discount' => 'sometimes|required|integer',
            'priceAfter' => 'sometimes|required|integer',
            'amount' => 'sometimes|required|integer',
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
