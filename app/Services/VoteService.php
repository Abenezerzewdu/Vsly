<?php 
namespace App\Services;

use App\Models\Duel;
use App\Models\User;
use App\Models\Vote;

class VoteService
{
   
    public function vote(Duel $duel, User $user, int $votedFor): void
    {
        //  Prevent duplicate vote (extra safety beyond DB)
        if ($duel->votes()->where('user_id', $user->id)->exists()) {
            throw new \Exception('You have already voted.');
        }

        //  Ensure voted_for is valid participant
        if (!in_array($votedFor, [
            $duel->challenger_id,
            $duel->opponent_id
        ])) {
            throw new \Exception('Invalid vote target.');
        }

        Vote::create([
            'duel_id' => $duel->id,
            'user_id' => $user->id,
            'voted_for' => $votedFor
        ]);
    }
}