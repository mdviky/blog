<!DOCTYPE html>
<html>
<head>
    <title>{{ $post->title }}</title>
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
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Post content --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 p-6">
                <a href="{{ route('posts.index') }}" style="color: #3b82f6;"><-- Back to all posts</a>                

                <h1 class="text-2xl font-bold mt-4">{{ $post->title }}</h1>

                {{-- belongsTo: post → user → profile --}}
                <p>By {{ $post->user->name }}</p>
                @if ($post->user->profile)
                    <p>{{ $post->user->profile->bio }}</p>
                @endif

                {{-- belongsTo: post → category --}}
                @if ($post->category)
                    <p>Category: {{ $post->category->name }}</p>
                @endif

                {{-- belongsToMany: post → tags --}}
                @foreach ($post->tags as $tag)
                    <span>[{{ $tag->name }}]</span>
                @endforeach

                <hr class="my-4">
                <div>{{ $post->body }}</div>
            </div>

            {{-- Success message --}}
            @if (session('success'))
                <div class="bg-green-100 p-4 mb-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Comments list --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 p-6">
                <h3 class="text-lg font-bold mb-4">
                    Comments ({{ $post->comments->count() }})
                </h3>

                @forelse ($post->comments as $comment)
                    <div class="border-b py-3">
                        <strong>{{ $comment->user->name }}</strong>
                        <span class="text-gray-500 text-sm">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                        <p class="mt-1">{{ $comment->body }}</p>
                    </div>
                @empty
                    <p>No comments yet. Be the first to comment!</p>
                @endforelse
            </div>

            {{-- Comment form (only for logged in users) --}}
            @auth
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Leave a Comment</h3>

                    <form action="{{ route('comments.store', $post) }}" method="POST">
                        @csrf
                        <div>
                            <textarea
                                name="body"
                                rows="4"
                                placeholder="Write your comment here..."
                                class="w-full border rounded p-2"
                                required>{{ old('body') }}</textarea>
                            @error('body')
                                <p class="text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                            class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">
                            Post Comment
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p>Please <a href="{{ route('login') }}" class="text-blue-500">login</a> to leave a comment.</p>
                </div>
            @endauth
                <a href="{{ route('admin.posts.edit', $post) }}" style="color: #3b82f6;">Edit post</a>

        </div>
    </div>
</body>
</html>