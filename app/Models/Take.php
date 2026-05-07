<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Take extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content', 'is_hot'];

    /**
     * Get the user who posted the take.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the duels associated with the take.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function duels()
    {
        return $this->hasMany(Duel::class);
    }

    /**
     * Get the reactions associated with the take.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
