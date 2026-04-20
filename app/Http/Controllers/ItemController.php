<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Services\FileService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    function index(Request $request) {
        $search = $request->input('search');

        $query = Item::with(['category', 'image']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->paginate($request->input('per_page'));

        return ItemResource::collection($items);
    }

    function store(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'category_id' => ['required', 'numeric', 'exists:categories,id'],
        ]);

        $item = Item::create($validated);

        if ($request->hasFile('img')) {
            $fileService = new FileService();
            $fileService->upload($request->file('img'), 'menu', $item);
        }

        $item->load(['category', 'image']);

        return new ItemResource($item);
    }

    function update(string $id, Request $request) {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric'],
            'category_id' => ['sometimes', 'numeric', 'exists:categories,id'],
        ]);

        $item->update($validated);

        if ($request->hasFile('img')) {
            $fileService = new FileService();

            if ($item->image) {
                $fileService->delete($item->image);
            }

            $fileService->upload($request->file('img'), 'menu', $item);
        }

        $item->load(['category', 'image']);

        return new ItemResource($item);
    }

    function destroy(string $id) {
        $item = Item::findOrFail($id);

        $item->delete();

        return response()->json(['message' => 'Delete Successful'], 200);
    }
}
