<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collar;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PetController;

class CollarController extends Controller
{
    protected $petController;

    public function __construct(PetController $petController) {
        $this->petController = $petController;
    }

    public function update(Request $request) {
        // Validate the incoming request
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'code' => 'required'
        ]);
        
        // Construct the URL using the provided code
        $url = 'http://localhost:4300/user-main-component/find_pet/' . $request->code;
    
        // Log the constructed URL for debugging
        \Log::info('Fetching collar with URL: ' . $url);
    
        // Retrieve the collar data based on the provided code
        $collar = Collar::where('url', $url)->first();
    
        // Check if the collar exists
        if (!$collar) {
            return response()->json(['message' => 'Collar not found.'], 404);
        }
    
        // Update the pet_id for the retrieved collar
        $collar->pet_id = $request->pet_id;
        $collar->save();
    
        // Return a success response
        return response()->json(['message' => 'Collar updated successfully.', 'collar' => $collar], 200);
    }

    

    public function getPetByCode($code) {
        $url = 'http://localhost:4300/user-main-component/find_pet/' . $code;        
        
        $petid = Collar::where('url', $url)->first();        

        if (!$petid) {
            return response()->json([
                'message' => 'Pet not found'
            ], 404);
        }

        $pet = $this->petController->show($petid->pet_id);

        return response()->json([
            'data' => $pet
        ]);
    }
}
