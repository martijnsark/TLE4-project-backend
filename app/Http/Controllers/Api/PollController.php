<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PollController extends Controller
{
    // Get all polls
    public function index()
    {
        $polls = Poll::all();
        return response()->json($polls);
    }
    // Show form to create a new poll
    public function create()
    {
        return response()->json(['message' => 'Create poll success']);
    }
    // Store a new poll
    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|integer|exists:articles,id',
            'question' => 'required|string|max:255',
        ]);

        $poll = new Poll();
        $poll->article_id = $request->article_id;
        $poll->question = $request->question;
        $poll->save();

        return response()->json(['message' => 'Poll created successfully']);
    }
    // Show a specific poll
    public function show(Poll $poll)
    {
        Gate::authorize('view', $poll);
        return response()->json($poll);
    }
    // Show form to edit a poll
    public function edit()
    {
        return response()->json(['message' => 'Edit poll success']);
    }
    // Update a poll
    public function update(Request $request, PollVote $pollVote)
    {
        Gate::authorize('update', $pollVote);

        $validated = $request->validate([
            'poll_id' => ['sometimes', 'integer', 'exists:polls,id'],
            'option_id' => ['sometimes', 'integer', 'exists:poll_options,id'],
        ]);

        if (isset($validated['poll_id'], $validated['option_id'])) {
            $optionBelongsToPoll = \App\Models\PollOption::where('id', $validated['option_id'])
                ->where('poll_id', $validated['poll_id'])
                ->exists();

            if (! $optionBelongsToPoll) {
                return response()->json([
                    'message' => 'This option does not belong to this poll.',
                ], 422);
            }
        }

        $pollVote->update($validated);

        return response()->json($pollVote->fresh());
    }
    // Delete a poll
    public function destroy(Poll $poll){
        $poll->delete();
        return response()->json(['message' => 'Poll deleted successfully']);
    }

    //Calculate total votes for a poll
    public function results($pollId)
    {
        $totalVotes = PollVote::where('poll_id', $pollId)->count();

        $options = PollOption::where('poll_id', $pollId)->get();

        $results = [];

        foreach ($options as $option) {
            $votes = PollVote::where('option_id', $option->id)->count();

            $results[] = [
                'option' => $option->option_text,
                'votes' => $votes,
                'percentage' => $totalVotes > 0
                    ? round(($votes / $totalVotes) * 100, 2)
                    : 0,
            ];
        }

        return response()->json($results);
    }
}
