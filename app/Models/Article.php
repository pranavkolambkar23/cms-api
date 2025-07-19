<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'title',
        'slug',
        'content',
        'summary',
        'status',
        'published_at',
        'user_id',
    ];

    // Dates casting (optional, useful for Carbon operations)
    protected $dates = [
        'published_at',
    ];

    // === Relationships ===

    // Article belongs to a User (Author)
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Article belongs to many Categories (many-to-many)
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
