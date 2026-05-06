<?php

namespace App\Http\Controllers;

use App\Models\Duel;
use Illuminate\Http\Request;
use App\Services\VoteService;

class VoteController extends Controller
{
    //store votes

public function store(Request $request, Duel $duel, VoteService $voteService)
{
    $validated = $request->validate([
        'voted_for' => ['required', 'exists:users,id']
    ]);

    $this->authorize('vote', $duel);

    try {
        $voteService->vote(
            duel: $duel,
            user: $request->user(),
            votedFor: $validated['voted_for']
        );
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }

    return back()->with('success', 'Vote submitted.');
}
}
