<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
</head>
<body>
    <h1>Edit Post</h1>
    <a href="{{ route('posts.index') }}">Back to blog</a>

    <form action="{{ route('admin.posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $post->title) }}" required>
            @error('title') <p>{{ $message }}</p> @enderror
        </div>

        <div>
            <label>Body</label>
            <textarea name="body" rows="10" required>{{ old('body', $post->body) }}</textarea>
            @error('body') <p>{{ $message }}</p> @enderror
        </div>

        <div>
            <label>Category</label>
            <select name="category_id">
                <option value="">-- No Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ $post->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Tags</label><br>
            @foreach ($tags as $tag)
                <label>
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                        {{ $post->tags->contains($tag->id) ? 'checked' : '' }}>
                    {{ $tag->name }}
                </label>
            @endforeach
        </div>

        <div>
            <label>Status</label>
            <select name="status">
                <option value="draft" {{ $post->status == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>

        <button type="submit">Update Post</button>
    </form>

    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Are you sure?')">Delete Post</button>
    </form>
</body>
</html>