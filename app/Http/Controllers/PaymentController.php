<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PetOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Store a new payment.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Payment request received:', $request->all());
    
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'provider_id' => 'nullable|exists:providers,id',
            'card_id' => 'nullable|exists:cards,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'transaction_id' => 'nullable|string',
            'status' => 'required|string|in:pending,completed,failed',
            'description' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'metadata' => 'nullable|json',
        ]);
    
        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        try {
            // Attempt to create the payment object
            $payment = Payment::create([
                'pet_owner_id' => $request->pet_owner_id,
                'provider_id' => $request->provider_id,
                'card_id' => $request->card_id,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount,
                'discount_amount' => $request->discount_amount,
                'currency' => $request->currency,
                'transaction_id' => $request->transaction_id,
                'status' => $request->status,
                'description' => $request->description,
                'payment_date' => $request->payment_date,
                'metadata' => $request->metadata,
            ]);
    
            // Log the payment object before returning it
            Log::info('Payment object created:', $payment->toArray());
    
            // Return the newly created payment as a resource
            return response()->json([
                'message' => 'Payment created successfully.',
                'payment' => $payment,
            ], 201);
    
        } catch (\Exception $e) {
            // Log the exception message to identify the problem
            Log::error('Error creating or saving payment:', ['message' => $e->getMessage()]);
    
            // Return an error response with the exception message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show a single payment by ID.
     */
    public function show($id)
    {
        // Find payment by ID
        $payment = Payment::findOrFail($id);

        return response()->json([
            'payment' => $payment,
        ], 200);
    }

    /**
     * Get all payments by pet owner ID (user ID).
     */
    public function getPaymentsByUserId($pet_owner_id)
    {
        // Validate that the pet owner exists
        $petOwner = PetOwner::findOrFail($pet_owner_id);

        // Get all payments for the given pet owner
        $payments = Payment::where('pet_owner_id', $pet_owner_id)->get();

        return response()->json([
            'payments' => $payments,
        ], 200);
    }

    public function update(Request $request, Payment $payment)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'pet_owner_id' => 'required|exists:pet_owners,id',
        'provider_id' => 'required|exists:providers,id',
        'card_id' => 'nullable|exists:cards,id',
        'payment_method' => 'required|string',
        'amount' => 'required|numeric|min:0',
        'discount_amount' => 'nullable|numeric|min:0',
        'currency' => 'required|string|max:3',
        'transaction_id' => 'nullable|string',
        'status' => 'required|string|in:pending,completed,failed',
        'description' => 'nullable|string',
        'payment_date' => 'nullable|date',
        'metadata' => 'nullable|json',
        'delivered' => 'required|boolean',  // Validate the delivered field
    ]);

    // Return validation errors if any
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Update the payment with the validated data
    $payment->update($request->all());

    // Return the updated payment as a resource
    return response()->json([
        'message' => 'Payment updated successfully.',
        'payment' => $payment,
    ]);
}

}
