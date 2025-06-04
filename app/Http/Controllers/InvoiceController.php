<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
{
    $invoices = Invoice::with(['order', 'membership.pet.petOwner'])
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($invoice) {
            // Determine the customer name based on the invoice type
            if ($invoice->order) {
                $customerName = $invoice->order->customer_name;
            } elseif ($invoice->membership 
                && $invoice->membership->pet 
                && $invoice->membership->pet->petOwner) {
                $petOwner = $invoice->membership->pet->petOwner;
                $customerName = trim($petOwner->first_name . ' ' . $petOwner->last_name);
            } else {
                $customerName = 'N/A';
            }
            
            // Set the invoice date based on the invoice type
            $invoiceDate = $invoice->order
                ? $invoice->order->created_at 
                : ($invoice->membership ? $invoice->membership->start_date : null);

            // Set total amount based on the invoice type
            $totalAmount = $invoice->order
                ? $invoice->order->total_amount 
                : ($invoice->membership ? $invoice->membership->price : 0);

            // Set status based on the invoice type
            $status = $invoice->order
                ? $invoice->order->status 
                : ($invoice->membership ? $invoice->membership->status : null);

            return [
                'id' => $invoice->id,
                'invoice_date' => $invoiceDate,
                'customer_name' => $customerName,
                'total_amount' => $totalAmount,
                'status' => $status,
                'type' => $invoice->order_id ? 'order' : 'membership',
            ];
        });

    return response()->json($invoices);
}


    public function show($id)
{
    $invoice = Invoice::with([
        'order.orderProducts.product',
        'membership.pet.petOwner',
        'membership.package'
    ])->findOrFail($id);

    if ($invoice->order_id) {
        return response()->json([
            'id' => $invoice->id,
            'invoice_date' => $invoice->order->created_at,
            'customer_name' => $invoice->order->customer_name,
            'address' => $invoice->order->address,
            'contact_no' => $invoice->order->contact_no,
            'email' => $invoice->order->email,
            'total_amount' => $invoice->order->total_amount,
            'status' => $invoice->order->status,
            'delivery' => $invoice->order->delivery ?? 0,
            'order_products' => $invoice->order->orderProducts->map(function ($item) {
                return [
                    'product_name' => $item->product->product_name_en,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount_percentage' => $item->discount_percentage,
                    'total_price' => $item->total_price,
                ];
            }),
            'type' => 'order'
        ]);
    } else if ($invoice->membership_id) {
        return response()->json([
            'id' => $invoice->id,
            'invoice_date' => $invoice->membership->start_date,
            'customer_name' => $invoice->membership->pet->petOwner->first_name . ' ' . $invoice->membership->pet->petOwner->last_name,
            'address' => $invoice->membership->pet->petOwner->city ?? 'N/A',
            'contact_no' => $invoice->membership->pet->petOwner->phone,
            'email' => $invoice->membership->pet->petOwner->email,
            'total_amount' => $invoice->membership->price,
            'status' => $invoice->membership->status,
            'delivery' => $invoice->membership->delivery ?? 0,
            'order_products' => [
                [
                    'pet_name' => $invoice->membership->pet->name,
                    'package_type' => $invoice->membership->package->title, // explicitly included
                    'price' => $invoice->membership->price,
                ]
            ],
            'type' => 'membership'
        ]);
    }

    return response()->json(['error' => 'Invoice type not found'], 404);
}

    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}
