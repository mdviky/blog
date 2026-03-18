<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Events\PostCreated;

class PostController extends Controller
{
    // Show all published posts
    public function index()
    {
        $posts = Post::with(['user', 'category', 'tags'])
            ->where('status', 'published')
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    // Show a single post
    public function show(Post $post)
    {
        $post->load(['user.profile', 'category', 'tags', 'comments.user']);

        return view('posts.show', compact('post'));
    }


    // Show form to create a new post
    public function create()
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    // Save new post to database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status'      => 'required|in:draft,published',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug']    = \Illuminate\Support\Str::slug($validated['title']);

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $post = \App\Models\Post::create($validated);

        // Fire the event
        PostCreated::dispatch($post);

        // Sync tags via pivot table (belongsToMany)
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('posts.index')->with('success', 'Post created!');
    }

    // Show form to edit a post
    public function edit(Post $post)
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    // Save edited post
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status'      => 'required|in:draft,published',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);

        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        // Sync tags via pivot table
        $post->tags()->sync($request->tags ?? []);

        return redirect()->route('posts.index')->with('success', 'Post updated!');
    }

    // Delete a post
    public function destroy(Post $post)
    {
        $post->tags()->detach(); // remove pivot table rows first
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted!');
    }
}
