<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // A review can have many replies
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
