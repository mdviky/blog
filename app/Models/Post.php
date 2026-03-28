<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $fillable = [
        'category_id', 'title',
        'slug', 'body', 'status', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Post belongs to ONE user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Post belongs to ONE category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Post has MANY comments
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Post belongs to MANY tags (via post_tag pivot)
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    
}