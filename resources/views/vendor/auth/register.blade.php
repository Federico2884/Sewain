<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Vendor — Sewain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 font-bold text-xl text-green-600">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                </svg>
                Sewain
            </a>
            <h1 class="text-2xl font-bold text-slate-800 mt-4">Daftar sebagai Vendor</h1>
            <p class="text-slate-500 text-sm mt-1">Mulai sewakan barangmu dan dapatkan penghasilan</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-7">
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('vendor.register') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Foto KTP</label>
                    <div class="relative">
                        <label for="ktp-upload"
                               class="flex flex-col items-center justify-center w-full border-2 border-dashed border-slate-300 rounded-lg px-3 py-5 cursor-pointer hover:border-green-400 hover:bg-green-50/30 transition @error('ktp') border-red-400 @enderror"
                               id="ktp-drop-area">
                            <div id="ktp-placeholder" class="text-center">
                                <svg class="w-8 h-8 mx-auto text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                                <p class="text-sm text-slate-500">Klik untuk upload foto KTP</p>
                                <p class="text-xs text-slate-400 mt-1">JPG, PNG, atau WebP (maks. 2MB)</p>
                            </div>
                            <img id="ktp-preview" src="#" alt="Preview KTP" class="hidden max-h-40 rounded-lg">
                        </label>
                        <input id="ktp-upload" type="file" name="ktp" accept="image/jpeg,image/png,image/webp" required class="hidden"
                               onchange="previewKtp(this)">
                    </div>
                    @error('ktp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat</label>
                    <textarea name="address" rows="2" required
                              class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500 @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('password') border-red-400 @enderror">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <button type="submit"
                        class="w-full bg-green-600 text-white font-semibold py-2.5 rounded-xl hover:bg-green-700 transition text-sm">
                    Daftar Sekarang
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-5">
                Sudah punya akun vendor?
                <a href="{{ route('vendor.login') }}" class="text-green-600 font-medium hover:underline">Masuk</a>
            </p>
        </div>
    </div>
<script>
function previewKtp(input) {
    const preview = document.getElementById('ktp-preview');
    const placeholder = document.getElementById('ktp-placeholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
