<?php


namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PetOwner; // Assuming you have this model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Store a new order.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Order request received:', $request->all());
    
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:pending,delivered,failed',
            'order_date' => 'nullable|date', // Date validation
            'metadata' => 'nullable|json', // Validate metadata as JSON string
        ]);
    
        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        try {
            // Decode the metadata field into an array (if not null)
            $metadata = json_decode($request->metadata, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Invalid metadata format.'], 422);
            }
    
            // Ensure 'products' array exists inside metadata
            if (!isset($metadata['products']) || !is_array($metadata['products'])) {
                return response()->json(['error' => 'Metadata must contain a products list.'], 422);
            }
    
            // Convert the ISO 8601 date to MySQL-compatible format
            $orderDate = $request->order_date ? \Carbon\Carbon::parse($request->order_date)->format('Y-m-d H:i:s') : null;
    
            // Create the order
            $order = Order::create([
                'pet_owner_id' => $request->pet_owner_id,
                'amount' => $request->amount,
                'discount_amount' => $request->discount_amount,
                'status' => $request->status,
                'order_date' => $orderDate, // Use the formatted date
                'metadata' => $request->metadata, // Store as JSON string
            ]);
    
            // Log the order object before returning it
            Log::info('Order created:', $order->toArray());
    
            // Return the newly created order as a resource
            return response()->json([
                'message' => 'Order created successfully.',
                'order' => $order,
            ], 201);
    
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error creating order:', ['message' => $e->getMessage()]);
    
            // Return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }    


    /**
     * Show a single order by ID.
     */
    public function show($id)
    {
        // Find order by ID
        $order = Order::findOrFail($id);

        return response()->json([
            'order' => $order,
        ], 200);
    }

    /**
     * Get all orders by pet owner ID (user ID).
     */
    public function getOrdersByUserId($pet_owner_id)
    {
        // Validate that the pet owner exists
        $petOwner = PetOwner::findOrFail($pet_owner_id);

        // Get all orders for the given pet owner
        $orders = Order::where('pet_owner_id', $pet_owner_id)->get();

        return response()->json([
            'orders' => $orders,
        ], 200);
    }

    /**
     * Update an existing order.
     */
    public function update(Request $request, Order $order)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'products' => 'required|array',
            'products.*.provider_id' => 'required|exists:providers,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.amount' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:1',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'status' => 'required|string|in:pending,completed,failed',
            'order_date' => 'nullable|date',
            'metadata' => 'nullable|json',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the order with the validated data
        $order->update($request->all());

        // Return the updated order as a resource
        return response()->json([
            'message' => 'Order updated successfully.',
            'order' => $order,
        ]);
    }
}