<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;


class PostController extends Controller
{
    // GET /api/posts — return all published posts as JSON
    public function index()
    {
        $posts = Post::with(['user', 'category', 'tags'])
                     ->where('status', 'published')
                     ->latest()
                     ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $posts
        ]);
    }

    // GET /api/posts/1 — return single post as JSON
    public function show(Post $post)
    {
        $post->load(['user.profile', 'category', 'tags', 'comments.user']);

        return response()->json([
            'success' => true,
            'data'    => $post
        ]);
    }

    // POST /api/posts — create new post (token required)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status'      => 'required|in:draft,published',
        ]);

        $validated['user_id']    = auth()->id();
        $validated['slug']       = Str::slug($validated['title']);
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        $post = Post::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully!',
            'data'    => $post
        ], 201);
    }
}
