<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-center py-20">
    <header class="container mx-auto max-w-screen-lg px-4">
        <nav class="flex justify-between items-center py-3">
            <a href="/" class="text-xl font-bold">My App</a>
            <ul class="flex space-x-4">
                <li><a href="/about" class="hover:text-blue-500">About</a></li>
                <li><a href="/contact" class="hover:text-blue-500">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main class="container mx-auto max-w-screen-lg px-4">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white text-center py-4 mt-10">
        &copy; {{ date('Y') }} My App
    </footer>
</body>
</html>