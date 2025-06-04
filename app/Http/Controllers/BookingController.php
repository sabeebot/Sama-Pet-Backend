<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'pet_owner_id' => 'required|exists:pet_owners,id', // ✅ include this
        'booking_date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required',
        'remind' => 'required|boolean',
        'note' => 'nullable|string',
        //'provider_id' => 'required|exists:providers,id',
        //'service_id' => 'required|exists:services,id',
    ]);

    

    $booking = Booking::create($validated);

    return response()->json([
        'message' => 'Booking created successfully',
        'booking' => $booking
    ], 201);
}


    public function mine(Request $request)
{
    $user = Auth::user();

    $bookings = Booking::with(['provider', 'service'])
        ->where('pet_owner_id', $user->id)
        ->get();

    return response()->json($bookings);
}
}
