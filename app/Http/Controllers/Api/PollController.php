<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::all();
        return response()->json($polls);
    }

    public function create()
    {
        return response()->json(['message' => 'Create poll success']);
    }

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

    public function show(Poll $poll)
    {
        return response()->json($poll);
    }

    public function edit()
    {
        return response()->json(['message' => 'Edit poll success']);
    }

    public function update(Request $request, Poll $poll){
        $request->validate([
            'article_id' => 'required|integer|exists:articles,id',
            'question' => 'required|string|max:255',
        ]);

        $poll->update($request->all());

        $poll->save();

        return response()->json($poll);
    }

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
