<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderItem;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Service;
use App\Models\Product;
use App\Models\Pet;
use App\Models\PetOwner;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function getProviderSalesReport($providerId)
    {
        $productOrders = Order::with(['products', 'petOwner.pets'])
            ->whereHas('products', function ($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            })
            ->get();

            $serviceOrders = ServiceOrder::with(['serviceItems', 'petOwner'])
            ->whereHas('serviceItems.service', function ($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            })
            ->get();
        

        $sales = [];

        foreach ($productOrders as $order) {
            $items = [];
            foreach ($order->products as $product) {
                $items[] = [
                    'name' => $product->product_name_en,
                    'price' => $product->price
                ];
            }

            $sales[] = [
                'owner' => $order->customer_name,
                'pets' => $order->petOwner && $order->petOwner->pets ? $order->petOwner->pets->pluck('name')->toArray() : ['Unknown'],
                'amount_paid' => $order->total_amount,
                'discount' => 0,
                'final_amount' => $order->total_amount,
                'payment_date' => $order->invoice_date,
                'items' => $items
            ];
        }

        foreach ($serviceOrders as $order) {
            $items = [];
            foreach ($order->serviceItems as $serviceItem) {
                $service = Service::find($serviceItem->service_id);
                $items[] = [
                    'name' => $service->title ?? 'Unknown Service',
                    'price' => $serviceItem->unit_price
                ];
            }

            $sales[] = [
                'owner' => $order->customer_name,
                'pets' => $order->petOwner && $order->petOwner->pets ? $order->petOwner->pets->pluck('name')->toArray() : ['Unknown'],
                'amount_paid' => $order->total_amount,
                'discount' => 0,
                'final_amount' => $order->total_amount,
                'payment_date' => $order->invoice_date,
                'items' => $items
            ];
        }

        return response()->json($sales);
    }
}
