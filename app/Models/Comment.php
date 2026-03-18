<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'body', 'approved'];

    // Comment belongs to ONE post
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    // Comment belongs to ONE user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}