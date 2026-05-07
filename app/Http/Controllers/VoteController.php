<?php

namespace App\Http\Controllers;

use App\Models\Duel;
use Illuminate\Http\Request;
use App\Services\VoteService;

class VoteController extends Controller
{
    /**
     * Store a new vote for a duel.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Duel $duel
     * @param \App\Services\VoteService $voteService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Duel $duel, VoteService $voteService)
    {
        $validated = $request->validate([
            'voted_for' => ['required', 'exists:users,id']
        ]);

        try {
            $this->authorize('vote', $duel);

            $voteService->vote(
                duel: $duel,
                user: $request->user(),
                votedFor: $validated['voted_for']
            );

            return back()->with('success', 'Vote submitted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
