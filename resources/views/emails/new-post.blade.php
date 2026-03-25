<!DOCTYPE html>
<html>
<head>
    <title>New Post Published!</title>
</head>
<body>
    <h1>New Post: {{ $post->title }}</h1>
    <p>By {{ $post->user->name }}</p>
    <p>{{ Str::limit($post->body, 200) }}</p>
    <a href="{{ url('/posts/' . $post->id) }}">Read Full Post</a>
</body>
</html>