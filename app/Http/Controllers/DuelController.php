<?php

namespace App\Http\Controllers;

use App\Models\Duel;
use App\Models\Round;
use App\Models\Take;
use App\Services\DuelService;
use Illuminate\Http\Request;

class DuelController extends Controller
{
    //create challenge in a take
     public function store(Request $request, Take $take)
    {
        $user = $request->user();

        //  Prevent self challenge
        $this->authorize('create', [Duel::class, $take]);
        

        //  Prevent duplicate active duel
        $existing = Duel::where('take_id', $take->id)
            ->where(function ($q) use ($user, $take) {
                $q->where('challenger_id', $user->id)
                  ->where('opponent_id', $take->user_id);
            })
            ->where('status', 'active')
            ->exists();

        if ($existing) {
            return redirect()->back()->with('error', 'Duel already active.');
        }

        //  Create duel
        $duel = Duel::create([
            'take_id' => $take->id,
            'challenger_id' => $user->id,
            'opponent_id' => $take->user_id,
            'status' => 'active',
            'current_round' => 1,
        ]);

        //  Create first round
        Round::create([
            'duel_id' => $duel->id,
            'round_number' => 1,
        ]);

        return redirect()->route('duels.show', $duel);
    }

    //make a move for challenges
    public function submitMove(Request $request, Duel $duel, DuelService $duelService)
{
    $validated = $request->validate([
        'response' => ['required', 'string', 'max:500']
    ]);

    $this->authorize('respond', $duel);

    $duelService->submitMove(
        duel: $duel,
        user: $request->user(),
        response: $validated['response']
    );

    return back()->with('success', 'Move submitted.');
}
}
