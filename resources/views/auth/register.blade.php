<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-slate-800">Buat Akun Penyewa</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar gratis dan mulai menyewa barang</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
            <!-- Date of Birth -->
            <div>
                <x-input-label for="dob" value="Tanggal Lahir" />
                <x-text-input id="dob" class="block w-full" type="date" name="dob" :value="old('dob')" required />
                <x-input-error :messages="$errors->get('dob')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div>
                <x-input-label for="no_telp" value="Nomor HP" />
                <x-text-input id="no_telp" class="block w-full" type="text" name="no_telp" :value="old('no_telp')" required maxlength="12" placeholder="08xxxxxxxxxx" />
                <x-input-error :messages="$errors->get('no_telp')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />

            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />

            <x-text-input id="password_confirmation" class="block w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full mt-6">
            Daftar Sekarang
        </x-primary-button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-6">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-green-600 font-medium hover:underline">Masuk di sini</a>
    </p>
</x-guest-layout>
