<?php

namespace App\Http\Controllers;

use App\Models\Code;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    public function index()
    {
        return Code::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'affiliate' => 'required|string|max:128',
            'code' => 'required|string|max:64',
            'expiration_date' => 'required|date',
            'percentage' => 'required|integer',
        ]);

        return Code::create($request->all());
    }

    public function show(Code $code)
    {
        return $code;
    }

    public function update(Request $request, Code $code)
    {
        $request->validate([
            'affiliate' => 'sometimes|string|max:128',
            'code' => 'sometimes|string|max:64',
            'expiration_date' => 'sometimes|date',
            'percentage' => 'sometimes|integer',
        ]);

        $code->update($request->all());

        return $code;
    }

    public function destroy(Code $code)
    {
        $code->delete();

        return response()->json(['message' => 'Code deleted successfully.']);
    }

    public function getCodeByCode($code)
    {
        $codeRecord = Code::where('code', $code)->first();

        if (!$codeRecord) {
            return response()->json(['message' => 'Code not found', 'code' => null], 200);
        }

        if ($codeRecord->expiration_date < now()) {
            return response()->json(['message' => 'Code has expired'], 200);
        }
    
        // Return the codeRecord with proper JSON format
        return response()->json($codeRecord);
    }
}

