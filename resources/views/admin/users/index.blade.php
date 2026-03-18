<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="bg-white shadow p-4">
        <div class="max-w-7xl mx-auto flex justify-between">
            <a href="{{ route('posts.index') }}" class="font-bold">My Blog</a>
            <div>
                @auth
                    <span>{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="text-red-500">Logout</button>
                    </form>
                    
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6">All Users</h1>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">ID</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Role</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Bio</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $user->id }}</td>
                                <td class="px-6 py-4 text-sm font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $user->profile?->bio ?? 'No bio' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $user->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</body>
</html>
