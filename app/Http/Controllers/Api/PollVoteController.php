<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PollVote;
use Illuminate\Http\Request;

class PollVoteController extends Controller
{
    public function index()
    {
        $pollVotes = PollVote::all();
        return response()->json($pollVotes);
    }

    public function store(Request $request)
    {

        $exists = PollVote::where('poll_id', $request->poll_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Je hebt al gestemd op deze poll.'
            ], 409);
        }

        $request->validate([
            'poll_id' => 'required',
            'user_id' => 'required',
            'option_id' => 'required',
            'voted_at' => 'required'
        ]);

        $pollVote = new PollVote();
        $pollVote->poll_id = $request->poll_id;
        $pollVote->user_id = auth()->id();
        $pollVote->option_id = $request->option_id;
        $pollVote->voted_at = $request->voted_at;
        $pollVote->save();

        return response()->json($pollVote);
    }

    public function show(PollVote $pollVote)
    {
        return response()->json($pollVote);
    }

    public function update(Request $request, PollVote $pollVote)
    {
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

    public function destroy(PollVote $pollVote)
    {
        $pollVote->delete();
        return response()->json($pollVote);
    }
}
