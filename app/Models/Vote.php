<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    //
    
    use HasFactory;

    protected $fillable = [
        'duel_id',
        'user_id',
        'voted_for'
    ];

    //  duel being voted on
    public function duel()
    {
        return $this->belongsTo(Duel::class);
    }

    //  voter
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //  who they voted for
    public function votedFor()
    {
        return $this->belongsTo(User::class, 'voted_for');
    }

}
