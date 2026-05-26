<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Vendor Sewain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 font-bold text-xl text-green-600">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                </svg>
                Sewain
            </a>
            <h1 class="text-2xl font-bold text-slate-800 mt-4">Masuk sebagai Vendor</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola barang sewaan kamu dari sini</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-7">
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('vendor.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('email') border-red-400 @enderror">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex items-center gap-2 mb-5">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300">
                    <label for="remember" class="text-sm text-slate-600">Ingat saya</label>
                </div>

                <button type="submit"
                        class="w-full bg-green-600 text-white font-semibold py-2.5 rounded-xl hover:bg-green-700 transition text-sm">
                    Masuk
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-5">
                Belum punya akun vendor?
                <a href="{{ route('vendor.register') }}" class="text-green-600 font-medium hover:underline">Daftar di sini</a>
            </p>
            <p class="text-center text-sm text-slate-400 mt-2">
                <a href="{{ route('login') }}" class="hover:underline">Masuk sebagai Penyewa</a>
            </p>
        </div>
    </div>
</body>
</html>
