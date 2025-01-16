<?php

namespace App\Http\Controllers;

use App\Models\CodeUsage;
use Illuminate\Http\Request;

class CodeUsageController extends Controller
{
    public function index()
    {
        return CodeUsage::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|exists:pet_owners,id',
            'date_of_usage' => 'required|date',
            'code_id' => 'required|exists:codes,id',
        ]);

        return CodeUsage::create($request->all());
    }

    public function show(CodeUsage $codeUsage)
    {
        return $codeUsage;
    }

    public function update(Request $request, CodeUsage $codeUsage)
    {
        $request->validate([
            'owner_id' => 'sometimes|exists:pet_owners,id',
            'date_of_usage' => 'sometimes|date',
            'code_id' => 'sometimes|exists:codes,id',
        ]);

        $codeUsage->update($request->all());

        return $codeUsage;
    }

    public function destroy(CodeUsage $codeUsage)
    {
        $codeUsage->delete();

        return response()->json(['message' => 'CodeUsage deleted successfully.']);
    }
}
