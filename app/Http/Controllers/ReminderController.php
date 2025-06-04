<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ReminderResource;
use Illuminate\Support\Facades\Validator;
use App\Models\Reminder; // Add this line to import the Reminder model
use Illuminate\Support\Facades\Log;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reminders = Reminder::all();
        return ReminderResource::collection($reminders);
    }
    
    public function getRemindersByPetId($pet_id)
    {
        $reminders = Reminder::where('pet_id', $pet_id)->get();
        return response()->json($reminders);
    }
    public function getReminderByOwnerId($owner_id)
    {
        $reminders = Reminder::where('pet_owner_id', $owner_id)->get();
        return response()->json($reminders);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Request received:', $request->all());
    
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'pet_id' => 'nullable|exists:pets,id',
            'provider_id' => 'nullable|exists:providers,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'remind' => 'required|boolean',
            'repeat' => 'required|in:Doesn\'t Repeat,Daily,Weekly,Monthly,Annually',
            'note' => 'nullable|string',
        ]);
    
        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Check if either pet_id or title is required based on your business logic
        // This step can be adjusted as per specific requirements if needed
        if (!$request->pet_id && !$request->title) {
            return response()->json(['errors' => 'Either pet_id or title is required.'], 422);
        }
    
        try {
            // Attempt to create the reminder object
            $reminder = Reminder::create([
                'pet_owner_id' => $request->pet_owner_id,
                'pet_id' => $request->pet_id,
                'provider_id' => $request->provider_id,
                'title' => $request->title,
                'date' => $request->date,
                'time' => $request->time,
                'remind' => $request->remind,
                'repeat' => $request->repeat,
                'note' => $request->note,
            ]);
    
            // Temporarily log the reminder object before saving
            Log::info('Reminder object created:', $reminder->toArray());
    
            // Return the newly created reminder as a resource
            return new ReminderResource($reminder);
    
        } catch (\Exception $e) {
            // Log the exception message to identify the problem
            Log::error('Error creating or saving reminder:', ['message' => $e->getMessage()]);
    
            // Return an error response with the exception message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Reminder $reminder)
    {
        return new ReminderResource($reminder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reminder $reminder)
    {
        $validator = Validator::make($request->all(), [
            'pet_owner_id' => 'required|exists:pet_owners,id',
            'pet_id' => 'nullable|exists:pets,id',
            'provider_id' => 'nullable|exists:providers,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'remind' => 'required|boolean',
            'repeat' => 'required|in:Doesn\'t Repeat,Daily,Weekly,Monthly,Annually',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reminder->update($request->all());

        return new ReminderResource($reminder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the reminder by ID
        $reminder = Reminder::find($id);

        if (!$reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        try {
            // Delete the reminder
            $reminder->delete();
            return response()->json(['message' => 'Reminder deleted successfully'], 200);
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error deleting reminder:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete reminder'], 500);
        }
    }
}
