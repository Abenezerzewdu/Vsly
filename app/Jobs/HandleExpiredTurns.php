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
     * Execute the job to check for and handle expired turns.
     * 
     * @param \App\Services\DuelService $duelService
     * @return void
     */
    public function handle(DuelService $duelService): void
    {
        $duels = Duel::where('status', 'active')
            ->whereNotNull('turn_started_at')
            ->get();

        foreach ($duels as $duel) {
            if (!$duel->isTurnExpired()) {
                continue;
            }

            $duelService->expireDuel($duel);
        }
    }
}
