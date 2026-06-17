<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SourceController extends Controller
{
    //get all sources, sorted by name
    public function index(): JsonResponse
    {
        return response()->json(
            Source::orderBy('name')->get()
        );
    }
    //get single source
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Source::class);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'reliability_score' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $source = Source::create($validated);

        return response()->json([
            'message' => 'Source created successfully',
            'source' => $source,
        ], 201);
    }
    //edit existing source, can update name, url and reliability_score
    public function update(Request $request, Source $source): JsonResponse
    {
        Gate::authorize('update', $source);
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'url' => ['sometimes', 'url'],
            'reliability_score' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $source->update($validated);

        return response()->json([
            'message' => 'Source updated successfully',
            'source' => $source->fresh(),
        ]);
    }
    //delete a source, also deletes all links to articles but does not delete the articles themselves
    public function destroy(Source $source): JsonResponse
    {
        Gate::authorize('delete', $source);
        $source->delete();

        return response()->json([
            'message' => 'Source deleted successfully',
        ]);
    }
}
