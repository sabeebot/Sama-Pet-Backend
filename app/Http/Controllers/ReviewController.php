<?php 
namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ReviewResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Reviews::all();
        return response()->json($reviews);
    }

    public function show($id)
    {
        $review = Reviews::findOrFail($id);
        return response()->json($review);
    }

    public function getReviewByServiceId($service_id)
    {
        $reviews = Reviews::where('service_id', $service_id)->get();
        return response()->json($reviews);
    }

    public function getReviewByProductId($product_id)
    {
        $reviews = Reviews::where('product_id', $product_id)->get();
        return response()->json($reviews);
    }

    public function store(Request $request)
    {    
        Log::info('Request received:', $request->all());
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'service_id' => 'nullable|integer|exists:services,id',
            'product_id' => 'nullable|integer|exists:products,id',
            'pet_owner_id' => 'required|integer|exists:pet_owners,id',
            'rate' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'date' => 'required|date',
        ]);
    // Return validation errors if any
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    if (!$request->service_id && !$request->product_id) {
        return response()->json(['errors' => 'Either service_id or product_id is required.'], 422);
    }

    try {
        // Attempt to create the review object
        $review = new Reviews([
            'service_id' => $request->service_id,
            'product_id' => $request->product_id,
            'pet_owner_id' => $request->pet_owner_id,
            'rate' => $request->rate,
            'comment' => $request->comment,
            'date' => $request->date,
        ]);

        // Temporarily log the review object before saving
        Log::info('Review object created:', $review->toArray());

        // Attempt to save the review to the database
        $review->save();

        // Return the newly created review as a resource
        return new ReviewResource($review);

    } catch (\Exception $e) {
        // Log the exception message to identify the problem
        Log::error('Error creating or saving review:', ['message' => $e->getMessage()]);

        // Return an error response with the exception message
        return response()->json(['error' => $e->getMessage()], 500);
    }
    
}
}
