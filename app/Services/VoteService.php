<?php 
namespace App\Services;

use App\Models\Duel;
use App\Models\User;
use App\Models\Vote;

class VoteService
{
   
   public function vote(Duel $duel, User $user, int $votedFor): void
{
    //  Prevent duplicate vote
    if ($duel->votes()->where('user_id', $user->id)->exists()) {
        throw new \Exception('You have already voted.');
    }

    //  Validate target
    if (!in_array($votedFor, [
        $duel->challenger_id,
        $duel->opponent_id
    ])) {
        throw new \Exception('Invalid vote target.');
    }

    //  Store vote
    $duel->votes()->create([
        'user_id' => $user->id,
        'voted_for' => $votedFor
    ]);

    //  Auto-calculate winner
    $this->recalculateWinner($duel);
}

private function recalculateWinner(Duel $duel): void
{
    $votes = $duel->votes()
        ->selectRaw('voted_for, COUNT(*) as total')
        ->groupBy('voted_for')
        ->pluck('total', 'voted_for');

    if ($votes->isEmpty()) {
        return;
    }

    // find max
    $maxVotes = $votes->max();

    // check tie
    $topCandidates = $votes->filter(fn ($count) => $count === $maxVotes);

    if ($topCandidates->count() > 1) {
        // tie → no winner yet
        $duel->winner_id = null;
    } else {
        $duel->winner_id = $topCandidates->keys()->first();
    }

    $duel->save();
}
    //duel winner for now dictated by the votes
    public function decideWinner(Duel $duel): void
{
    if ($duel->status !== 'finished') {
        throw new \Exception('Duel not finished.');
    }

    $result = $duel->votes()
        ->selectRaw('voted_for, COUNT(*) as total')
        ->groupBy('voted_for')
        ->orderByDesc('total')
        ->first();

    if ($result) {
        $duel->winner_id = $result->voted_for;
        $duel->save();
    }
}
}