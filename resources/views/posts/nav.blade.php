<nav class="bg-blue-600 shadow-lg p-4 text-white">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <a href="{{ route('posts.index') }}" class="font-bold text-xl tracking-tight">My Blog</a>
        <div class="space-x-4">
            @auth
                <span class="opacity-90">Welcome, {{ auth()->user()->name }}</span>
                <div class="inline-block">
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 px-3 py-1 rounded transition">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="hover:underline">Login</a>
            @endauth
        </div>
    </div>
</nav>