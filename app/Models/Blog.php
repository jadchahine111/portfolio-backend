<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id',
        'excerpt',
        'content',
        'date',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'blogs_subcategories');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
