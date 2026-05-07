<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    protected $fillable = [
        'duel_id',
        'round_number',
        'challenger_response',
        'opponent_response',
        'completed'
    ];

    /**
     * Get the duel that the round belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function duel()
    {
        return $this->belongsTo(Duel::class);
    }
}
