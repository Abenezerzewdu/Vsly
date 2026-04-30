<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Take extends Model
{
    //
    use HasFactory;
     protected $fillable = ['user_id', 'content', 'is_hot'];

       public function user()
    {
        return $this->belongsTo(User::class);
    }

      public function duels()
    {
        return $this->hasMany(Duel::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }


}
