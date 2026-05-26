<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sewain') — Sewa Barang Mudah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

{{-- Navbar --}}
<nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-green-600">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                </svg>
                Sewain
            </a>

            <div class="flex items-center gap-6">
                <a href="{{ route('items.index') }}" class="text-sm font-medium text-slate-600 hover:text-green-600 transition">Cari Barang</a>

                @auth
                    <a href="{{ route('user.dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-green-600 transition">Dashboard</a>
                    <a href="{{ route('user.rentals.index') }}" class="text-sm font-medium text-slate-600 hover:text-green-600 transition">Rental Saya</a>
                    <a href="{{ route('user.chats.index') }}" class="text-sm font-medium text-slate-600 hover:text-green-600 transition">Chat</a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-green-600 transition">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-44 bg-white border border-slate-200 rounded-lg shadow-lg py-1 text-sm">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-slate-600 hover:bg-slate-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-green-600 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
    @if(session('success'))
        <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-start gap-2">
            <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm flex items-start gap-2">
            <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif
</div>

{{-- Content --}}
<main class="flex-1">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-white border-t border-slate-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-2">
        <span class="font-bold text-green-600">Sewain</span>
        <p class="text-sm text-slate-400">© {{ date('Y') }} Sewain. Platform sewa barang terpercaya.</p>
    </div>
</footer>

@stack('scripts')
</body>
</html>
