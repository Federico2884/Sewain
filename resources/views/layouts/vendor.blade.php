<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vendor') — Sewain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex">

{{-- Sidebar --}}
<aside class="w-60 bg-white border-r border-slate-200 min-h-screen flex flex-col fixed top-0 left-0 z-30">
    <div class="px-5 py-5 border-b border-slate-100">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg text-green-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
            </svg>
            Sewain
        </a>
        <p class="text-xs text-slate-400 mt-1">Panel Vendor</p>
    </div>

    <nav class="flex-1 p-3 space-y-0.5">
        @php $current = request()->routeIs('vendor.*'); @endphp

        @php
            $links = [
                ['route' => 'vendor.dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                ['route' => 'vendor.items.index', 'label' => 'Barang Saya', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                ['route' => 'vendor.rentals.index', 'label' => 'Semua Rental', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
                ['route' => 'vendor.chats.index', 'label' => 'Chat', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>'],
            ];
        @endphp

        @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                      {{ request()->routeIs($link['route']) ? 'bg-green-50 text-green-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $link['icon'] !!}</svg>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="p-3 border-t border-slate-100">
        <div class="px-3 py-2 mb-1">
            <p class="text-xs font-semibold text-slate-700 truncate">{{ Auth::guard('vendor')->user()?->name }}</p>
            <p class="text-xs text-slate-400 truncate">{{ Auth::guard('vendor')->user()?->email }}</p>
        </div>
        <form method="POST" action="{{ route('vendor.logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Main area --}}
<div class="flex-1 ml-60 min-h-screen flex flex-col">
    <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-slate-800">@yield('title', 'Dashboard')</h1>
        <div class="text-sm text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</div>
    </header>

    <div class="px-8 py-0">
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

    <main class="flex-1 px-8 py-6">
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
