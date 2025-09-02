<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Mini Issue Tracker') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-gray-100 flex flex-col">
        <div class="p-6 text-4xl font-bold border-b border-yellow-700">
            🐞 Issue Tracker
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{--{{ route('projects.index') }}--}}"
               class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                📁 Projects
            </a>
            <a href="{{--  {{ route('issues.index') }} --}}"
               class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                📝 Issues
            </a>
            <a href="{{--{{ route('tags.index') }}--}}"
               class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                🏷️ Tags
            </a>
        </nav>
        <div class="p-4 border-t border-gray-700">
            <form method="POST" action="{{--  {{ route('logout') }} --}}">
                @csrf
                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 py-2 px-4 rounded-lg transition">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Navbar -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-semibold">@yield('title', 'Dashboard')</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Hello, {{ Auth::user()->name ?? 'Guest' }}</span>
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}"
                     class="w-10 h-10 rounded-full border border-gray-300" alt="avatar">
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

@yield('scripts')
</body>
</html>
