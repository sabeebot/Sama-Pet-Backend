<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return Cart::all();
    }

    public function show($id)
    {
        return Cart::findOrFail($id);
    }

    public function store(Request $request)
    {
        $cart = Cart::create($request->all());
        return response()->json($cart, 201);
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);
        $cart->update($request->all());
        return response()->json($cart, 200);
    }

    public function destroy($id)
    {
        Cart::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function getCartsByPetOwnerId($id)
    {
        $carts = Cart::where('pet_owner_id', $id)->get();
        return response()->json($carts);
    }
}
