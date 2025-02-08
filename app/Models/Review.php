<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'blog_id', 'content'];

    // A review belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A review belongs to a blog
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }



    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
