<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommentTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentTemplateController extends Controller
{
    //get all active comment templates
    public function index(): JsonResponse
    {
        $templates = CommentTemplate::where('is_active', true)
            ->orderBy('text')
            ->get();

        return response()->json($templates);
    }
    //store a new comment template
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $template = CommentTemplate::create([
            'text' => $validated['text'],
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Comment template created successfully',
            'comment_template' => $template,
        ], 201);
    }
    //update a comment template
    public function update(Request $request, CommentTemplate $commentTemplate): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $commentTemplate->update($validated);

        return response()->json([
            'message' => 'Comment template updated successfully',
            'comment_template' => $commentTemplate->fresh(),
        ]);
    }
    //delete a comment template
    public function destroy(CommentTemplate $commentTemplate): JsonResponse
    {
        $commentTemplate->delete();

        return response()->json([
            'message' => 'Comment template deleted successfully',
        ]);
    }
}
