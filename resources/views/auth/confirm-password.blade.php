<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-slate-800">Konfirmasi Password</h1>
        <p class="text-sm text-slate-500 mt-1">Ini area aman. Konfirmasi password kamu untuk melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" />

            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full mt-6">
            Konfirmasi
        </x-primary-button>
    </form>
</x-guest-layout>
