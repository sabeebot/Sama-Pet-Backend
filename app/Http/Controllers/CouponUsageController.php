<?php

namespace App\Http\Controllers;

use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class CouponUsageController extends Controller
{
    public function index()
    {
        return CouponUsage::all();
    }

    public function store(Request $request)
    {
        // Log the incoming request for debugging purposes
        Log::info('Request received:', $request->all());
    
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'owner_id' => 'required|integer|exists:pet_owners,id',
            'coupon_id' => 'required|integer|exists:coupons,id',
            'date_of_usage' => 'required|date',
        ]);
    
        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        try {
            // Attempt to create the coupon usage object
            $couponUsage = new CouponUsage([
                'owner_id' => $request->owner_id,
                'coupon_id' => $request->coupon_id,
                'date_of_usage' => $request->date_of_usage,
            ]);
    
            // Temporarily log the coupon usage object before saving
            Log::info('CouponUsage object created:', $couponUsage->toArray());
    
            // Attempt to save the coupon usage to the database
            $couponUsage->save();
            // Return the newly created coupon usage as a resource
            return response()->json($couponUsage, 201);
    
        } catch (\Exception $e) {
            // Log the exception message to identify the problem
            Log::error('Error creating or saving coupon usage:', ['message' => $e->getMessage()]);
    
            // Return an error response with the exception message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(CouponUsage $couponUsage)
    {
        return $couponUsage;
    }

    public function update(Request $request, CouponUsage $couponUsage)
    {
        $request->validate([
            'owner_id' => 'sometimes|exists:pet_owners,id',
            'date_of_usage' => 'sometimes|date',
            'coupon_id' => 'sometimes|exists:coupons,id',
        ]);

        $couponUsage->update($request->all());

        return $couponUsage;
    }

    public function destroy(CouponUsage $couponUsage)
    {
        $couponUsage->delete();

        return response()->json(['message' => 'CouponUsage deleted successfully.']);
    }

    public function getByOwnerId($ownerId)
    {
        $couponUsages = CouponUsage::where('owner_id', $ownerId)->get();

        if ($couponUsages->isEmpty()) {
            return response()->json(['message' => 'No coupon usage found for this owner.']);
        }

        return $couponUsages;
    }
}
