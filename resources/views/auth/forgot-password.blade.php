<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-slate-800">Lupa Password</h1>
        <p class="text-sm text-slate-500 mt-1">Masukkan email kamu dan kami akan mengirim tautan untuk mengatur ulang password.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full mt-6">
            Kirim Tautan Reset
        </x-primary-button>
    </form>

    <p class="text-center text-sm text-slate-400 mt-6">
        <a href="{{ route('login') }}" class="hover:underline">Kembali ke halaman masuk</a>
    </p>
</x-guest-layout>
