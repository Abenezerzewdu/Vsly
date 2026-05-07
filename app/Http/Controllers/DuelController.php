<?php

namespace App\Http\Controllers;

use App\Models\Duel;
use App\Models\Round;
use App\Models\Take;
use App\Services\DuelService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DuelController extends Controller
{
    /**
     * Create a new duel challenge for a specific take.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Take $take
     * @param \App\Services\DuelService $duelService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Take $take, DuelService $duelService)
    {
        try {
            $this->authorize('create', [Duel::class, $take]);

            $duel = $duelService->createDuel($take, $request->user());

            return redirect()->route('duels.show', $duel)
                ->with('success', 'Challenge issued!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Submit a move for the current participant in the duel.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Duel $duel
     * @param \App\Services\DuelService $duelService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitMove(Request $request, Duel $duel, DuelService $duelService)
    {
        $validated = $request->validate([
            'response' => ['required', 'string', 'max:500']
        ]);

        try {
            $this->authorize('respond', $duel);

            $duelService->submitMove(
                duel: $duel,
                user: $request->user(),
                response: $validated['response']
            );

            return back()->with('success', 'Move submitted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified duel with its rounds and votes.
     * 
     * @param \App\Models\Duel $duel
     * @return \Inertia\Response
     */
    public function show(Duel $duel)
    {
        $duel->load([
            'challenger:id,name',
            'opponent:id,name',
            'rounds',
            'votes'
        ]);

        return Inertia::render('Duels/Show', [
            'duel' => [
                'id' => $duel->id,
                'status' => $duel->status,
                'current_round' => $duel->current_round,
                'total_rounds' => $duel->total_rounds,
                'challenger' => $duel->challenger,
                'opponent' => $duel->opponent,
                'winner_id' => $duel->winner_id,
                'votes_stats' => $duel->votes
                    ->groupBy('voted_for')
                    ->map(fn($group) => $group->count()),
                'rounds' => $duel->rounds->map(fn($round) => [
                    'round_number' => $round->round_number,
                    'challenger_response' => $round->challenger_response,
                    'opponent_response' => $round->opponent_response,
                    'completed' => $round->completed,
                ]),
            ]
        ]);
    }
}
