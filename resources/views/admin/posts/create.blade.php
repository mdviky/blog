<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1>Create New Post</h1>
    <a href="{{ route('posts.index') }}">Back to blog</a>

    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required>
            @error('title') <p>{{ $message }}</p> @enderror
        </div>

        <div>
            <label>Image</label>
            <input type="file" name="image">
            @error('image') <p>{{ $message }}</p> @enderror
        </div>

        <div>
            <label>Body</label>
            <textarea name="body" rows="10" required>{{ old('body') }}</textarea>
            @error('body') <p>{{ $message }}</p> @enderror
        </div>

        <div>
            <label>Category</label>
            <select name="category_id">
                <option value="">-- No Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Tags</label><br>
            @foreach ($tags as $tag)
                <label>
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}">
                    {{ $tag->name }}
                </label>
            @endforeach
        </div>

        <div>
            <label>Status</label>
            <select name="status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>

        <button type="submit">Create Post</button>
    </form>



                </div>
                


                <!-- http://localhost/laravel/blog/public/posts -->
            </div>
        </div>
    </div>
    </div>
</x-app-layout>