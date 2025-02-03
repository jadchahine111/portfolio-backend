<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = ['review_id', 'user_id', 'content'];

    // A reply belongs to a review
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // A reply belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
