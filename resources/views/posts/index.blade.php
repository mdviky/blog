<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="bg-white shadow p-4">
        <div class="max-w-7xl mx-auto flex justify-between">
            <a href="{{ route('posts.index') }}" class="font-bold">My Blog</a>
            <div>
                @auth
                    <span>{{ auth()->user()->name }}</span>
                    
<div class="inline-block">
                        <form action="{{ route('logout') }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="text-red-500">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            All Posts
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @forelse ($posts as $post)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 p-6">
                    <h2>
                        Post Title:
                        <a href="{{ route('posts.show', $post) }}" style="font-weight: bold;">
                             {{ $post->title }}
                            
                        </a>
                        
                    </h2>
                    <br/>
                        Post Id: {{ $post->id }}

                    <p>By {{ $post->user->name }}</p>

                    @if ($post->category)
                        <p>Category: {{ $post->category->name }}</p>
                    @endif

                    <p>Tags</p>
                    @foreach ($post->tags as $tag)
                        <span>{{ $tag->name }}</span>
                    @endforeach

                    <p>{{ $post->published_at->diffForHumans() }}</p>
                </div>
            @empty
                <p>No posts found.</p>
            @endforelse

            {{ $posts->links() }}

        </div>
    </div>
</body>
</html>