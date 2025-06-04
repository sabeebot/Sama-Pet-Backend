<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OrderResource;

use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{


    public function getOrdersByUserId($petOwnerId)
{
    $orders = Order::with(['OrderProducts.product'])
        ->where('pet_owner_id', $petOwnerId)
        ->orderBy('invoice_date', 'desc')
        ->get();
    
    return response()->json($orders, 200);
}


    public function index()
    {
        $orders = Order::with(['OrderProducts.product'])
                       ->orderBy('invoice_date', 'desc')
                       ->get();
    
        return response()->json($orders);
    }
    


    /**
     * Get the next available invoice and order numbers by fetching the auto-increment value.
     */
    public function getNextNumbers()
{
    $lastOrder = Order::orderBy('id', 'desc')->first();
    $lastInvoice = Invoice::orderBy('id', 'desc')->first();

    $nextOrderId = $lastOrder ? $lastOrder->id + 1 : 1;
    $nextInvoiceId = $lastInvoice ? $lastInvoice->id + 1 : 1;

    return response()->json([
        'order_number'   => $nextOrderId,
        'invoice_number' => $nextInvoiceId,
    ]);
}

/**
     * Display the specified order along with its order products.
     */
    public function show($id)
    {
        $order = Order::with(['OrderProducts.product'])->findOrFail($id);
        return new OrderResource($order);
    }






    /**
     * Retrieve products for a given provider.
     */
    public function getProductsByProvider($providerId)
    {
         $products = Product::where('provider_id', $providerId)->get();
         return response()->json($products);
    }

    /**
     * Store a new invoice with its invoice items.
     */
    public function store(Request $request)
{

    Log::info('[OrderController] Incoming request payload:', $request->all());



    $validatedData = $request->validate([
        'invoice_date'           => 'required|date',
        'status'                 => 'required|string',
        'customer_name'          => 'required|string',
        'contact_no'             => 'required|string',
        'email'                  => 'required|email',
        'address'                => 'required|string',
        'delivery'               => 'nullable|numeric',
        'products'               => 'required|array',
        'products.*.product_id'  => 'required|integer|exists:products,id',
        'products.*.quantity'    => 'required|integer|min:1',
        'products.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        'pet_owner_id'                  => 'nullable|integer|exists:pet_owners,id', // New validation rule
    ]);

    DB::beginTransaction();

    try {
        $totalAmount = 0;

        foreach ($validatedData['products'] as $productItem) {
            $product = Product::find($productItem['product_id']);
            $discountPercentage = $productItem['discount_percentage'] ?? 0;
            $discountedPrice = $product->price - ($product->price * $discountPercentage / 100);
            $totalAmount += $discountedPrice * $productItem['quantity'];
        }

        $delivery = $validatedData['delivery'] ?? 0;
        $totalAmount += $delivery;

        $order = Order::create([
            'invoice_date'  => $validatedData['invoice_date'],  // <-- clearly pass this
            'customer_name' => $validatedData['customer_name'],
            'address'       => $validatedData['address'],
            'contact_no'    => $validatedData['contact_no'],
            'email'         => $validatedData['email'],
            'status'        => $validatedData['status'],
            'delivery'      => $delivery,
            'total_amount'  => $totalAmount,
            'pet_owner_id'  => $validatedData['pet_owner_id'] ?? null,  // Now includes pet_owner_id if provided
        ]);

        foreach ($validatedData['products'] as $productItem) {
            $product = Product::find($productItem['product_id']);
            $discountPercentage = $productItem['discount_percentage'] ?? 0;
            $discountedPrice = $product->price - ($product->price * $discountPercentage / 100);

            OrderProduct::create([
                'order_id'            => $order->id,
                'product_id'          => $product->id,
                'quantity'            => $productItem['quantity'],
                'unit_price'          => $product->price,
                'discount_percentage' => $discountPercentage,
                'total_price'         => $discountedPrice * $productItem['quantity'],
            ]);

            Log::info('[OrderController] Order created with ID: ' . $order->id);

        }

        // Create Invoice linked with order
        $invoice = Invoice::create(['order_id' => $order->id]);

        DB::commit();

        return response()->json([
            'message' => 'Order created successfully',
            'invoice_id' => $invoice->id,  
            'type' => 'order'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Order creation failed',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function getOwnerOrders($petOwnerId)
{
    $orders = Order::with(['OrderProducts.product'])
                   ->where('pet_owner_id', $petOwnerId)
                   ->orderBy('invoice_date', 'desc')
                   ->get();

    // Log for debugging
    Log::info("Orders found for owner {$petOwnerId}: " . $orders->count());

    return response()->json($orders);
}






}
