<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PollOption;
use Illuminate\Http\Request;

class PollOptionController extends Controller
{
    public function index()
    {
        $pollOptions = PollOption::all();
        return response()->json($pollOptions);
    }

    public function create()
    {
        return response()->json(['message' => 'Create poll option success']);
    }
    public function store(Request $request)
    {
        $request->validate([
            'poll_id' => 'required',
            'option_text' => 'required',
        ]);

        $pollOption = new PollOption();
        $pollOption->poll_id = $request->poll_id;
        $pollOption->option_text = $request->option_text;
        $pollOption->save();

        return response()->json($pollOption);
    }

    public function show(PollOption $pollOption)
    {
        return response()->json($pollOption);
    }
    public function edit(PollOption $pollOption)
    {
        return response()->json($pollOption);
    }
    public function update(Request $request, PollOption $pollOption)
    {
        $request->validate([
            'poll_id' => 'required',
            'option_text' => 'required',
        ]);

        $pollOption->update($request->all());
        $pollOption->save();
        return response()->json($pollOption);
    }

    public function destroy(PollOption $pollOption)
    {
        $pollOption->delete();
        return response()->json($pollOption);
    }
}
