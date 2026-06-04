<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-slate-800">Verifikasi Email</h1>
        <p class="text-sm text-slate-500 mt-1">Terima kasih sudah mendaftar! Sebelum mulai, silakan verifikasi emailmu lewat tautan yang baru kami kirim. Belum menerima email? Kami akan kirim ulang dengan senang hati.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg text-center">
            Tautan verifikasi baru telah dikirim ke email kamu.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <x-primary-button class="w-full">
            Kirim Ulang Email Verifikasi
        </x-primary-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm text-slate-500 hover:text-slate-700 hover:underline">
            Keluar
        </button>
    </form>
</x-guest-layout>
