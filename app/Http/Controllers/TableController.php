<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\TableResource;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TableController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TableResource::collection(Table::all());
    }

    public function store(Request $request): TableResource
    {
        $validated = $request->validate([
            'number' => ['required', 'integer'],
        ]);

        $table = Table::create($validated);

        return new TableResource($table);
    }

    public function show(Table $table): TableResource
    {
        return new TableResource($table);
    }

    public function update(Request $request, Table $table): TableResource
    {
        $validated = $request->validate([
            'number' => ['required', 'integer'],
        ]);

        $table->update($validated);

        return new TableResource($table);
    }

    public function destroy(Table $table): Response
    {
        $table->delete();

        return response()->noContent();
    }
}