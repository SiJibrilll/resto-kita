<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function index(Request $request) {
        $search = $request->input('search');

        $categories = Category::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->paginate($request->input('per_page'));

        return CategoryResource::collection($categories);
    }

    function show(string $id) {
        $category = Category::findOrFail($id);

        return new CategoryResource($category);
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
