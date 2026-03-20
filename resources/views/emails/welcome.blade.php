<!DOCTYPE html>
<html>
<head>
    <title>Welcome to My Blog!</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Thank you for registering on My Blog.</p>
    <p>You can now:</p>
    <ul>
        <li>Read all our blog posts</li>
        <li>Leave comments on posts</li>
    </ul>
    <p>
        <a href="{{ url('/posts') }}">Start Reading</a>
    </p>
    <p>Regards,<br>My Blog Team</p>
</body>
</html>