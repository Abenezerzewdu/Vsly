<?php
namespace App\Services;

use App\Models\Duel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DuelService
{
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

    private function applyMove($duel, $round, $user, $response): void
    {
        if ($user->id === $duel->challenger_id) {

            if ($round->challenger_response) {
                throw new \Exception('Already responded.');
            }

            $round->challenger_response = $response;

        } elseif ($user->id === $duel->opponent_id) {

            if ($round->opponent_response) {
                throw new \Exception('Already responded.');
            }

            $round->opponent_response = $response;

        } else {
            throw new \Exception('Unauthorized participant.');
        }
    }

    private function isRoundComplete($round): bool
    {
        return $round->challenger_response && $round->opponent_response;
    }

    private function completeRound(Duel $duel, $round): void
    {
        $round->completed = true;

        if ($duel->current_round < $duel->total_rounds) {

            $duel->current_round++;

            $duel->rounds()->create([
                'round_number' => $duel->current_round
            ]);

        } else {
            $duel->status = 'finished';
        }
    }
}