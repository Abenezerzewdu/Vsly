<?php

namespace App\Jobs;

use App\Models\Duel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleExpiredTurns implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $duels = Duel::where('status', 'active')
            ->whereNotNull('turn_started_at')
            ->get();

        foreach ($duels as $duel) {

            if (!$duel->isTurnExpired()) {
                continue;
            }

            $this->handleExpiredTurn($duel);
        }
    }

     private function handleExpiredTurn(Duel $duel): void
    {
        //  Determine who failed
        $failedUserId = $duel->turn === 'challenger'
            ? $duel->challenger_id
            : $duel->opponent_id;

        //  Opponent wins
        $winnerId = $duel->turn === 'challenger'
            ? $duel->opponent_id
            : $duel->challenger_id;

        $duel->status = 'finished';
        $duel->winner_id = $winnerId;

        $duel->save();
    }
}
