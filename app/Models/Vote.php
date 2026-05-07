<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'duel_id',
        'user_id',
        'voted_for'
    ];

    /**
     * Get the duel being voted on.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function duel()
    {
        return $this->belongsTo(Duel::class);
    }

    /**
     * Get the user who cast the vote.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who was voted for.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function votedFor()
    {
        return $this->belongsTo(User::class, 'voted_for');
    }
}
