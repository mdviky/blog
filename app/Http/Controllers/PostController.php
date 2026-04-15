<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Show all published posts
    public function index()
    {
        // dd(auth()->id());
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
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    // Save new post to database
    public function store(StorePostRequest $request)
    {
        // $request->validated() already contains only validated data
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $validated['image'] = $path;
        }

        $post = new Post($validated);
        $post->user_id = auth()->id();
        $post->save();

        PostCreated::dispatch($post);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('posts.index')->with('success', 'Post created!');
    }

    // Show form to edit a post
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    // Save edited post
    public function update(UpdatePostRequest $request, Post $post)
    {

        /* echo "<pre>";
        print_r($request);
        print_r($request->all());
        echo "</pre>";
        exit; */
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['status'] === 'published' && ! $post->published_at) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $path = $request->file('image')->store('posts', 'public');
            $validated['image'] = $path;
        }

        $post->update($validated);
        // Sync tags via pivot table

        $post->tags()->sync($request->tags ?? []);

        return redirect()->route('posts.index')->with('success', 'Post updated!');
    }

    // Delete a post
    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->tags()->detach(); // remove pivot table rows first
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted!');
    }
}
