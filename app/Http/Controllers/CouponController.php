<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        return Coupon::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'quantity' => 'required|integer',
            'code' => 'required|string|max:64',
            'expiration_date' => 'required|date',
            'description' => 'required|string|max:256',
            'membership' => 'required|boolean',
            'title' => 'required|string', 
            'image' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        return Coupon::create($request->all());
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
