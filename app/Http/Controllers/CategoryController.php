<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function index() {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    function store(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string']
        ]);

        $category = Category::create($validated);

        return new CategoryResource($category);
    }

    function update(string $id, Request $request) {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string']
        ]);

        $category->update($validated);

        return new CategoryResource($category);
    }

    function destroy(string $id) {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json(['message' => 'Delete Successful'], 200);
    }
}
