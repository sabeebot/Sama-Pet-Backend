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


    public function Index()
{
    $collars = Collar::with('pet.petOwner')->get();
    return response()->json(['data' => $collars]);
}


public function update(Request $request, $petId)
{
    $request->validate([
        'code'    => 'required',
        'pet_id'  => 'required|exists:pets,id',
    ]);

    $url = 'http://localhost:4300/user-main-component/find_pet/' . $request->code;

    $collar = Collar::where('url', $url)->first();
    if (!$collar) {
        return response()->json(['message' => 'Collar not found.'], 404);
    }

    $collar->pet_id = $petId;   // or $request->pet_id
    $collar->save();

    return response()->json(['message' => 'Collar updated.', 'collar' => $collar]);
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


    public function updateCollarCode(Request $request, $petId)
    {
        // grab the raw code string from the request body
        $newCode = $request->input('code');

        // find the collar record by pet_id
        $collar = Collar::where('pet_id', $petId)->first();

        if (! $collar) {
            return response()->json([
                'message' => "No collar record found for pet {$petId}"
            ], 404);
        }

        // overwrite its URL column
        $collar->url = $newCode;
        $collar->save();

        return response()->json([
            'message' => 'Collar URL updated successfully',
            'collar'  => $collar,
        ], 200);
    }


}
