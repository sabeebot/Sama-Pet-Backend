<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Models
use App\Models\Service;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Coupon;

class ReportController extends Controller
{
    public function getProviderOverview(Request $request)
    {
        $providerId = $request->query('provider_id');

        if (!$providerId) {
            return response()->json(['error' => 'Provider ID is required'], 400);
        }

        try {
            $totalServices = Service::where('provider_id', $providerId)->count();
            $totalProducts = Product::where('provider_id', $providerId)->count();
            $totalPromotions = Promotion::where('provider_id', $providerId)->count();
            $totalActiveCoupons = Coupon::where('provider_id', $providerId)
                ->where('expiration_date', '>', now())
                ->count();

            return response()->json([
                'totalServices' => $totalServices,
                'totalProducts' => $totalProducts,
                'totalPromotions' => $totalPromotions,
                'totalActiveCoupons' => $totalActiveCoupons
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching provider overview: ' . $e->getMessage());
            return response()->json(['error' => 'Server error fetching overview'], 500);
        }
    }
}
