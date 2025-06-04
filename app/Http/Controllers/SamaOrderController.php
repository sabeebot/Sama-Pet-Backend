<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\SamaOrderResource;
use App\Models\SamaOrder;
use Illuminate\Support\Facades\Validator;

class SamaOrderController extends Controller
{
    public function index() {
        return SamaOrderResource::collection(SamaOrder::all());
    }

    public function getOrdersBySupplier(Request $request, $supplierId) {
        // Assuming your orders table has a 'supplierId' column
        $orders = SamaOrder::where('supplier_id', $supplierId)->get();

        return SamaOrderResource::collection($orders);
    }
    
    

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'product_name_en'  => 'required|string|max:255',
            'product_name_ar'  => 'required|string|max:255',
            'pet_type'         => 'required|string|max:50',
            'status'           => 'required|integer|in:0,1',
            'supplier_id'      => 'required|integer',
            'quantity'         => 'required|numeric|min:1',
            'start_date'       => 'required|date',
            'received_date'    => 'required|date',
            'price'            => 'required|numeric|min:0',
            'delivery_price'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $order = SamaOrder::create($data);

        return new SamaOrderResource($order);
    }

    public function update(Request $request, $id) {
        $order = SamaOrder::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_name_en'  => 'required|string|max:255',
            'product_name_ar'  => 'required|string|max:255',
            'pet_type'         => 'required|string|max:50',
            'status'           => 'required|integer|in:0,1',
            'supplier_id'      => 'required|integer',
            'quantity'         => 'required|integer|min:1',
            'start_date'       => 'required|date',
            'received_date'    => 'required|date',
            'price'            => 'required|numeric|min:0',
            'delivery_price'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $order->update($data);

        return new SamaOrderResource($order);
    }

    public function destroy($id) {
        $order = SamaOrder::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
            'deleted' => true
        ], 200);
    }
}
