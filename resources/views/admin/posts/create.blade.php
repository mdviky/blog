<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
</head>
<body>
    <h1>Create New Post</h1>
    <a href="{{ route('posts.index') }}">Back to blog</a>

    <form action="{{ route('admin.posts.store') }}" method="POST">
        @csrf

        <div>
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required>
            @error('title') <p>{{ $message }}</p> @enderror
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
</body>
</html>