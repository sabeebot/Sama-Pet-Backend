<?php

namespace App\Http\Controllers;

use App\Models\Blogs;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Blogs::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tag' => 'required|string|max:128',
            'title' => 'required|string|max:128',
            'image' => 'required|string|max:128',
            'description' => 'required|string|max:256',
        ]);

        $blog = Blogs::create($validatedData);

        return response()->json($blog, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $blog = Blogs::findOrFail($id);
        return response()->json($blog);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $blog = Blogs::findOrFail($id);

        $validatedData = $request->validate([
            'tag' => 'string|max:128',
            'title' => 'string|max:128',
            'image' => 'string|max:128',
            'description' => 'string|max:256',
        ]);

        $blog->update($validatedData);

        return response()->json($blog);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = Blogs::findOrFail($id);
        $blog->delete();

        return response()->json(null, 204);
    }
}
