<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'duel_id',
        'round_number',
        'challenger_response',
        'opponent_response',
        'completed'
    ];

    //  belongs to duel
    public function duel()
    {
        return $this->belongsTo(Duel::class);
    }
}
