<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function store(Request $request,Promotion $promotion)
    {
        $promotion = new Promotion();
        $promotion->ad_image = Storage::disk('public')->put('promotions', $request->file('ad_image'));
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:ready_to_launch,custom_ad_design',
            'ad_image' => 'required_if:type,ready_to_launch|image|max:2048',
            'logo_image' => 'required_if:type,custom_ad_design|image|max:2048',
            'business_image' => 'required_if:type,custom_ad_design|image|max:2048',
            'business_name' => 'required_if:type,custom_ad_design|string|max:255',
            'ad_description' => 'required_if:type,custom_ad_design|string',
            'phone_number' => 'required_if:type,custom_ad_design|regex:/^\+9733\d{7}$/',
            'social_media' => 'required_if:type,custom_ad_design|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $promotion = new Promotion();
            $promotion->provider_id = auth()->id();
            $promotion->type = $request->type;

            if ($request->type === 'ready_to_launch') {
                $promotion->ad_image = $request->file('ad_image')->store('promotions', 'public');
            } else {
                $promotion->logo_image = $request->file('logo_image')->store('promotions', 'public');
                $promotion->business_image = $request->file('business_image')->store('promotions', 'public');
                $promotion->business_name = $request->business_name;
                $promotion->ad_description = $request->ad_description;
                $promotion->phone_number = $request->phone_number;
                $promotion->social_media = json_encode($request->social_media);
            }

            $promotion->save();

            return response()->json([
                'message' => 'Promotion created successfully',
                'promotion' => $promotion
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the promotion: ' . $e->getMessage()], 500);
        }
    }
}