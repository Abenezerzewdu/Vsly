<?php
namespace App\Services;

use App\Models\Duel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DuelService
{
    /**
     * Create a new duel from a take challenge.
     * 
     * @param \App\Models\Take $take
     * @param \App\Models\User $user The challenger
     * @return \App\Models\Duel
     * @throws \Exception
     */
    public function createDuel(\App\Models\Take $take, User $user): Duel
    {
        // Prevent duplicate active duel
        $existing = Duel::where('take_id', $take->id)
            ->where(function ($q) use ($user, $take) {
                $q->where('challenger_id', $user->id)
                  ->where('opponent_id', $take->user_id);
            })
            ->where('status', 'active')
            ->exists();

        if ($existing) {
            throw new \Exception('Duel already active.');
        }

        return DB::transaction(function () use ($take, $user) {
            // Create duel
            $duel = Duel::create([
                'take_id' => $take->id,
                'challenger_id' => $user->id,
                'opponent_id' => $take->user_id,
                'status' => 'active',
                'current_round' => 1,
                'total_rounds' => 3,
                'turn' => 'challenger',
                'turn_time_limit' => 120, // default (2 min)
                'turn_started_at' => now(),
            ]);

            // Create first round
            $duel->rounds()->create([
                'round_number' => 1,
            ]);

            return $duel;
        });
    }

    /**
     * Submit a move for a participant in a duel.
     * 
     * @param \App\Models\Duel $duel
     * @param \App\Models\User $user
     * @param string $response
     * @return void
     * @throws \Exception
     */
    public function submitMove(Duel $duel, User $user, string $response): void
    {
        DB::transaction(function () use ($duel, $user, $response) {

            // reload duel with fresh rounds
            $duel->load('rounds');

            $round = $duel->rounds
                ->where('round_number', $duel->current_round)
                ->first();

            if (!$round) {
                throw new \Exception('Round not found.');
            }
            //check turn not expired first
            $this->ensureTurnNotExpired($duel);
            //  Assign move
            $this->applyMove($duel, $round, $user, $response);

            // Complete round if both responded
            if ($this->isRoundComplete($round)) {
                $this->completeRound($duel, $round);
            }

            $round->save();
            $duel->save();
        });
    }

    /**
     * Ensure the current turn has not expired based on the time limit.
     * 
     * @param \App\Models\Duel $duel
     * @return void
     * @throws \Exception
     */
    private function ensureTurnNotExpired(Duel $duel): void
{
    if (!$duel->turn_started_at) {
        return;
    }

    $expiresAt = $duel->turn_started_at
        ->copy()
        ->addSeconds($duel->turn_time_limit);

    if (now()->greaterThan($expiresAt)) {
        throw new \Exception('Turn time expired.');
    }
}

    /**
     * Apply the participant's move to the current round and update duel state.
     * 
     * @param \App\Models\Duel $duel
     * @param \App\Models\Round $round
     * @param \App\Models\User $user
     * @param string $response
     * @return void
     * @throws \Exception
     */
    private function applyMove($duel, $round, $user, $response): void
{
    $isChallenger = $user->id === $duel->challenger_id;
    $isOpponent = $user->id === $duel->opponent_id;

    //  Enforce turn
    if (
        ($duel->turn === 'challenger' && !$isChallenger) ||
        ($duel->turn === 'opponent' && !$isOpponent)
    ) {
        throw new \Exception('Not your turn.');
    }

    if ($isChallenger) {

        if ($round->challenger_response) {
            throw new \Exception('Already responded.');
        }

        $round->challenger_response = $response;

        // switch turn
        $duel->turn = 'opponent';

    } elseif ($isOpponent) {

        if ($round->opponent_response) {
            throw new \Exception('Already responded.');
        }

        $round->opponent_response = $response;

        $duel->turn_started_at = now();
        // switch turn
        $duel->turn = 'challenger';
    } else {
        throw new \Exception('Unauthorized participant.');
    }
}
    /**
     * Check if both participants have submitted their responses for the round.
     * 
     * @param \App\Models\Round $round
     * @return bool
     */
    private function isRoundComplete($round): bool
    {
        return $round->challenger_response && $round->opponent_response;
    }

    /**
     * Complete the current round and advance to the next or finish the duel.
     * 
     * @param \App\Models\Duel $duel
     * @param \App\Models\Round $round
     * @return void
     */
    private function completeRound(Duel $duel, $round): void
{
    $round->completed = true;

    if ($duel->current_round < $duel->total_rounds) {

        $duel->current_round++;

        $duel->rounds()->create([
            'round_number' => $duel->current_round
        ]);

        // Reset turn to challenger
        $duel->turn = 'challenger';

    } else {
        $duel->status = 'finished';
    }
    /**
     * Terminate a duel because a participant failed to make a move in time.
     * 
     * @param \App\Models\Duel $duel
     * @return void
     */
    public function expireDuel(Duel $duel): void
    {
        // Determine who failed (the person whose turn it was)
        $winnerId = $duel->turn === 'challenger'
            ? $duel->opponent_id
            : $duel->challenger_id;

        $duel->status = 'finished';
        $duel->winner_id = $winnerId;
        $duel->save();
    }
}
