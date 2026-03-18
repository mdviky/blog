<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = ['user_id', 'avatar', 'bio', 'website'];

    // Profile belongs to ONE user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

