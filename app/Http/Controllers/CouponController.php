<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\BoughtCoupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CouponResource;


class CouponController extends Controller
{

    public function getProvCoupons(Request $request)
    {
        $providerId = $request->query('provider_id');
    
        if (!$providerId) {
            return response()->json(['error' => 'provider_id is required'], 400);
        }
    
        $coupons = Coupon::with('couponUsages.owner')->where('provider_id', $providerId)->get(); // 🧠 Add ->with()
        return response()->json($coupons);
    }


    

public function getCouponStatsByProvider(Request $request)
{
    $providerId = $request->query('provider_id');

    if (!$providerId) {
        return response()->json(['error' => 'provider_id is required'], 400);
    }

    $coupons = \App\Models\Coupon::withCount('couponUsages')
        ->where('provider_id', $providerId)
        ->get();

    // Log each coupon's usage count for debugging
    foreach ($coupons as $coupon) {
        Log::info("Coupon ID: {$coupon->id}, Usage Count: {$coupon->coupon_usages_count}");
    }

    $active = $coupons->count(); // Total coupons
    $used = $coupons->sum('coupon_usages_count'); // Total usage count

    Log::info("Provider {$providerId} — Total Active: {$active}, Total Used: {$used}");

    return response()->json([
        'active' => $active,
        'used' => $used
    ]);
}




    public function storeBoughtCoupon(Request $request)
{
    $validated = $request->validate([
        'pet_owner_id' => 'required|exists:pet_owners,id',
        'coupon_id' => 'required|exists:coupons,id'
    ]);

    $bought = BoughtCoupon::create($validated);

    return response()->json(['message' => 'Coupon redeemed successfully', 'data' => $bought], 201);
}


    public function getPublicCoupons()
{
    $coupons = Coupon::select([
        'id',
        'title_en as name',
        'description_en as description',
        'expiration_date as expiry',
        'discount',
        'offer_type',
        'price',
        'price_after',
        'code',
        'quantity'
    ])->get();

    return response()->json($coupons);
}




    

    

    public function index()
    {
        return Coupon::all();
    }

    /**
     * Display a listing of the resource.
     */
    public function populateTableAdminDashboard()
    {
        return CouponResource::collection(Coupon::with('provider')->get());
    }


    public function storeCouponAdminDashboard(Request $request)
    {
        // Initialize debug array
        $debug = [];
        $debug[] = 'Received Request: ' . json_encode($request->all());

        // Extract coupon data and provider data.
        $coupon = $request->input('coupon');
        $provider = $request->input('provider_data');

        $debug[] = 'Extracted coupon: ' . json_encode($coupon);
        $debug[] = 'Extracted provider: ' . json_encode($provider);

        if (!$provider || !isset($provider['profileId'])) {
            $debug[] = 'Provider data or profile ID is missing.';
            return response()->json([
                'error' => 'Provider data or profile ID is missing.',
                'debug' => $debug
            ], 422);
        }

        // Validate coupon data.
        $validator = Validator::make($coupon, [
            'provider'         => 'required|string',
            'created_date'     => 'required|date',
            'valid_till'       => 'required|date',
            'title_en'         => 'required|string|max:255',
            'title_ar'         => 'required|string|max:255',
            'description_en'   => 'required|string',
            'description_ar'   => 'required|string',
            'quantity'         => 'required|integer|min:1',
            'price'            => 'required|numeric',
            'price_after'      => 'required|numeric',
            'discount'         => 'required|numeric',
            'offer_type'       => 'nullable|numeric',
            'audience'         => 'required|string'
        ]);

        if ($validator->fails()) {
            $debug[] = 'Validation failed: ' . json_encode($validator->errors()->all());
            return response()->json([
                'errors' => $validator->errors(),
                'debug' => $debug
            ], 422);
        }

        // Prepare data for insertion.
        $data = [
            'provider'         => $coupon['provider'],
            'created_date'     => $coupon['created_date'],
            'expiration_date'       => $coupon['valid_till'],
            'title_en'         => $coupon['title_en'],
            'title_ar'         => $coupon['title_ar'],
            'description_en'   => $coupon['description_en'],
            'description_ar'   => $coupon['description_ar'],
            'quantity'         => $coupon['quantity'],
            'price'            => $coupon['price'],
            'price_after'      => $coupon['price_after'],
            'discount'         => $coupon['discount'],
            'offer_type'       => $coupon['offer_type'],
            'audience'         => $coupon['audience'],
            'provider_id'      => $provider['profileId']
        ];

        $debug[] = 'Data to insert: ' . json_encode($data);

        try {
            $storedCoupon = Coupon::create($data);
            $debug[] = 'Coupon created: ' . json_encode($storedCoupon);

            return response()->json([
                'message' => 'Admin coupon added successfully',
                'coupon' => $storedCoupon,
                'debug' => $debug
            ], 201);
        } catch (\Exception $e) {
            $debug[] = 'Exception: ' . $e->getMessage();
            Log::error('Error storing coupon: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to store coupon',
                'debug' => $debug
            ], 500);
        }
    }

    public function updateCouponAdminDashboard(Request $request, $id)
{
    // Initialize debug array
    $debug = [];
    $debug[] = 'Received Request: ' . json_encode($request->all());

    // Extract coupon data and provider data.
    $couponData = $request->input('coupon');
    $provider = $request->input('provider_data');

    $debug[] = 'Extracted coupon: ' . json_encode($couponData);
    $debug[] = 'Extracted provider: ' . json_encode($provider);

    if (!$provider || !isset($provider['profileId'])) {
        $debug[] = 'Provider data or profile ID is missing.';
        return response()->json([
            'error' => 'Provider data or profile ID is missing.',
            'debug' => $debug
        ], 422);
    }

    // Validate coupon data.
    $validator = Validator::make($couponData, [
        'provider'         => 'required|string',
        'created_date'     => 'required|date',
        'valid_till'       => 'required|date',
        'title_en'         => 'required|string|max:255',
        'title_ar'         => 'required|string|max:255',
        'description_en'   => 'required|string',
        'description_ar'   => 'required|string',
        'quantity'         => 'required|integer|min:1',
        'price'            => 'required|numeric',
        'price_after'      => 'required|numeric',
        'discount'         => 'required|numeric',
        'offer_type'       => 'nullable|numeric',
        'audience'         => 'required|string'
    ]);

    if ($validator->fails()) {
        $debug[] = 'Validation failed: ' . json_encode($validator->errors()->all());
        return response()->json([
            'errors' => $validator->errors(),
            'debug' => $debug
        ], 422);
    }

    // Prepare data for update.
    $data = [
        'provider'         => $couponData['provider'],
        'created_date'     => $couponData['created_date'],
        'expiration_date'  => $couponData['valid_till'],
        'title_en'         => $couponData['title_en'],
        'title_ar'         => $couponData['title_ar'],
        'description_en'   => $couponData['description_en'],
        'description_ar'   => $couponData['description_ar'],
        'quantity'         => $couponData['quantity'],
        'price'            => $couponData['price'],
        'price_after'      => $couponData['price_after'],
        'discount'         => $couponData['discount'],
        'offer_type'       => $couponData['offer_type'],
        'audience'         => $couponData['audience'],
        'provider_id'      => $provider['profileId']
    ];

    $debug[] = 'Data to update: ' . json_encode($data);

    try {
        $coupon = Coupon::findOrFail($id);
        $coupon->update($data);
        $debug[] = 'Coupon updated: ' . json_encode($coupon);

        return response()->json([
            'message' => 'Admin coupon updated successfully',
            'coupon' => $coupon,
            'debug' => $debug
        ], 200);
    } catch (\Exception $e) {
        $debug[] = 'Exception: ' . $e->getMessage();
        Log::error('Error updating coupon: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to update coupon',
            'debug' => $debug
        ], 500);
    }
}


public function deleteCouponAdminDashboard($id)
{
    try {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'message' => 'Coupon deleted successfully'
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error deleting coupon: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to delete coupon'
        ], 500);
    }
}



    public function show(Coupon $coupon)
    {
        return $coupon;
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'provider_id' => 'sometimes|exists:providers,id',
            'quantity' => 'sometimes|integer',
            'code' => 'sometimes|string|max:64',
            'expiration_date' => 'sometimes|date',
            'description' => 'sometimes|string|max:256',
            'membership' => 'sometimes|boolean',
            'title' => 'sometimes|string|exists:providers,name', 
            'image' => 'sometimes|string|exists:providers,profile_image',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $coupon->update($request->all());

        return $coupon;
    }

    public function getMembershipCoupons()
    {
        $membershipCoupons = Coupon::where('membership', 1)->get();
        return response()->json($membershipCoupons);
    }

    public function getNotMembershipCoupons()
    {
        $membershipCoupons = Coupon::where('membership', 0)->get();
        return response()->json($membershipCoupons);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully.']);
    }

    public function getCouponById($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        return response()->json($coupon);
    }

    public function reduceQuantity(Request $request, $id)
    {
        $coupon = Coupon::find($id);
    
        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }
    
        if ($coupon->quantity > 0) {
            $coupon->quantity -= 1;
            $coupon->save();
    
            return response()->json(['message' => 'Coupon quantity reduced successfully.', 'coupon' => $coupon]);
        } else {
            return response()->json(['message' => 'Coupon quantity cannot be reduced below 0'], 400);
        }
    }
}
