<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        // Validate the comment
        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        // Create comment through relationship
        // user_id and post_id set automatically
        $post->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $validated['body'],
            'approved'=> true,
        ]);

        return back()->with('success', 'Comment posted successfully!');
    }
}
