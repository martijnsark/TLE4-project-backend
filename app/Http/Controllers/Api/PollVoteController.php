<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PollVoteController extends Controller
{
    // Get all poll votes
    public function index()
    {
        $pollVotes = PollVote::all();
        return response()->json($pollVotes);
    }
    // Show form to create a new poll vote
    public function store(Request $request)
    {
        $validated = $request->validate([
            'poll_id' => ['required', 'integer', 'exists:polls,id'],
            'option_id' => ['required', 'integer', 'exists:poll_options,id'],
        ]);

        $optionBelongsToPoll = \App\Models\PollOption::where('id', $validated['option_id'])
            ->where('poll_id', $validated['poll_id'])
            ->exists();

        if (! $optionBelongsToPoll) {
            return response()->json([
                'message' => 'This option does not belong to this poll.',
            ], 422);
        }

        $exists = PollVote::where('poll_id', $validated['poll_id'])
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Je hebt al gestemd op deze poll.',
            ], 409);
        }

        $pollVote = PollVote::create([
            'poll_id' => $validated['poll_id'],
            'user_id' => $request->user()->id,
            'option_id' => $validated['option_id'],
            'voted_at' => now(),
        ]);

        return response()->json($pollVote, 201);
    }
    // Show a specific poll vote
    public function show(PollVote $pollVote)
    {
        return response()->json($pollVote);
    }
    // Show form to edit a poll vote
    public function update(Request $request, PollVote $pollVote)
    {
        Gate::authorize('update', $pollVote);
        $request->validate([
            'poll_id' => 'required',
            'user_id' => 'required',
            'option_id' => 'required',
            'voted_at' => 'required'
        ]);

        $pollVote->update($request->all());
        $pollVote->save();
        return response()->json($pollVote);
    }
    // Delete a poll vote

    public function destroy(PollVote $pollVote)
    {
        Gate::authorize('delete', $pollVote);

        $pollVote->delete();

        return response()->json([
            'message' => 'Poll vote deleted successfully',
        ]);
    }
}
