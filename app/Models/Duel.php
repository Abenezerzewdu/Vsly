<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duel extends Model
{
    //
        use HasFactory;
 protected $fillable = [
        'take_id',
        'challenger_id',
        'opponent_id',
        'status',
        'winner_id',
        'current_round'
    ];
    public function take()
    {
        return $this->belongsTo(Take::class);
    }
     public function challenger()
    {
        return $this->belongsTo(User::class, 'challenger_id');
    }
   public function opponent()
    {
        return $this->belongsTo(User::class, 'opponent_id');
    }
      public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    //
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }


}
