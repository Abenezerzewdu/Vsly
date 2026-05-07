<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duel extends Model
{
    use HasFactory;

    protected $fillable = [
        'take_id',
        'challenger_id',
        'opponent_id',
        'status',
        'current_round',
        'total_rounds',
        'turn',
        'turn_time_limit',
        'turn_started_at',
    ];

    /**
     * Get the take associated with the duel.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function take()
    {
        return $this->belongsTo(Take::class);
    }

    /**
     * Get the user who is the challenger.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function challenger()
    {
        return $this->belongsTo(User::class, 'challenger_id');
    }

    /**
     * Get the user who is the opponent.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opponent()
    {
        return $this->belongsTo(User::class, 'opponent_id');
    }

    /**
     * Get the user who won the duel.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Get the rounds associated with the duel.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    /**
     * Get the votes cast for the duel.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
    /**
     * Get the attributes that should be cast.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'turn_started_at' => 'datetime',
        ];
    }

    /**
     * Check if the current turn has expired based on the time limit.
     * 
     * @return bool
     */
    public function isTurnExpired(): bool
    {
        if (!$this->turn_started_at) {
            return false;
        }

        return now()->diffInSeconds($this->turn_started_at) > $this->turn_time_limit;
    }
}
