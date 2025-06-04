<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function show($id)
{
    $membership = Membership::with(['pet.owner'])->find($id);

    if (!$membership || !$membership->pet || !$membership->pet->owner) {
        return response()->json(['message' => 'Policy not found'], 404);
    }

    $owner = $membership->pet->owner;

    return response()->json([
        'policy' => [
            'id' => $membership->id,
            'start_date' => $membership->start_date,
            'end_date' => $membership->end_date,
            'status' => $membership->status,
        ],
        'pet' => [
            'id' => $membership->pet->id,
            'name' => $membership->pet->name,
            'pet_type' => $membership->pet->pet_type,
            'breed' => $membership->pet->breed,
            'age' => $membership->pet->age,
            'gender' => $membership->pet->gender
        ],
        'owner' => [
            'id'         => $owner->id,
            'first_name' => $owner->first_name,
            'last_name'  => $owner->last_name,
            'phone'      => $owner->phone,
            'email'      => $owner->email,
            'house'      => $owner->house,
            'road'       => $owner->road,
            'block'      => $owner->block,
            'building'   => $owner->building,
            'apt_number' => $owner->apt_number,
            'floor'      => $owner->floor,
            'city'       => $owner->city
        ]
    ]);
}

}