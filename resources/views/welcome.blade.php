@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-green-600 to-green-800 text-white">
    {{-- Decorative blobs --}}
    <div class="absolute -top-24 -right-24 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>
    <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-green-400/20 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 text-center">
        <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-green-50 text-xs font-medium px-3 py-1 rounded-full mb-6">
            <span class="w-1.5 h-1.5 bg-green-300 rounded-full"></span>
            Platform sewa barang terpercaya
        </span>
        <h1 class="text-4xl sm:text-5xl font-bold mb-4 leading-tight">
            Sewa Barang Lebih Mudah,<br>Lebih Terpercaya
        </h1>
        <p class="text-green-100 text-lg mb-8 max-w-xl mx-auto">
            Temukan barang dari vendor terpercaya. Pilih jadwal, durasi, dan metode pengiriman sesuai kebutuhan.
        </p>

        {{-- Search --}}
        <form action="{{ route('items.index') }}" method="GET"
              class="max-w-xl mx-auto flex items-center gap-2 bg-white rounded-2xl p-2 shadow-lg shadow-green-900/20">
            <div class="flex items-center gap-2 flex-1 pl-3">
                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                <input type="text" name="search" placeholder="Mau sewa apa hari ini?"
                       class="w-full py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none">
            </div>
            <button type="submit"
                    class="bg-green-600 text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-green-700 transition text-sm shrink-0">
                Cari
            </button>
        </form>

        {{-- Trust signals --}}
        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 mt-8 text-sm text-green-100">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Vendor terverifikasi
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Pembayaran aman
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Rating dua arah
            </span>
        </div>
    </div>
</section>

{{-- Categories --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Jelajahi Kategori</h2>
            <p class="text-slate-500 text-sm mt-1">Temukan barang sesuai kebutuhanmu</p>
        </div>
        <a href="{{ route('items.index') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-green-600 hover:text-green-700 transition">
            Lihat semua
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($categories as $name => $icon)
            <a href="{{ route('items.index', ['category' => $name]) }}"
               class="group bg-white border border-slate-200 rounded-2xl p-5 flex flex-col items-center text-center gap-3 hover:border-green-300 hover:shadow-md hover:-translate-y-0.5 transition">
                <div class="w-14 h-14 rounded-2xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center text-3xl transition">
                    {{ $icon }}
                </div>
                <span class="text-sm font-semibold text-slate-700 group-hover:text-green-700 transition">{{ $name }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- Featured items --}}
@if($featuredItems->isNotEmpty())
<section class="bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Barang Terbaru</h2>
                <p class="text-slate-500 text-sm mt-1">Pilihan barang yang siap kamu sewa</p>
            </div>
            <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-green-600 hover:text-green-700 transition">
                Lihat semua
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($featuredItems as $item)
                <a href="{{ route('items.show', $item) }}"
                   class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition group">
                    <div class="aspect-video bg-slate-100 overflow-hidden">
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                             class="w-full h-full object-contain group-hover:scale-105 transition duration-300">
                    </div>
                    <div class="p-4">
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">{{ $item->category }}</span>
                        <h3 class="font-semibold text-slate-800 mt-2 mb-1 line-clamp-1">{{ $item->name }}</h3>
                        <p class="text-xs text-slate-400 mb-3">oleh {{ $item->vendor->name }}</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-slate-800">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-slate-400">/hari</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-yellow-400 text-sm">★</span>
                                <span class="text-xs font-medium text-slate-600">{{ number_format($item->vendor->rating, 1) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- How it works --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
    <div class="text-center mb-10">
        <h2 class="text-2xl font-bold text-slate-800">Cara Kerja Sewain</h2>
        <p class="text-slate-500 text-sm mt-1">Hanya tiga langkah mudah</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
            ['1', 'Cari & Pilih Barang', 'Telusuri katalog, bandingkan harga, dan cek ketersediaan lewat kalender.'],
            ['2', 'Booking & Bayar', 'Pilih tanggal serta durasi sewa, lalu selesaikan pembayaran dengan aman.'],
            ['3', 'Pakai & Kembalikan', 'Ambil atau terima barang, gunakan, lalu kembalikan sesuai jadwal.'],
        ] as [$step, $title, $desc])
            <div class="relative bg-white border border-slate-200 rounded-2xl p-6">
                <div class="w-10 h-10 rounded-xl bg-green-600 text-white font-bold flex items-center justify-center mb-4">{{ $step }}</div>
                <h3 class="font-semibold text-slate-800 mb-1">{{ $title }}</h3>
                <p class="text-sm text-slate-500">{{ $desc }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- Why Sewain --}}
<section class="bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
        <h2 class="text-2xl font-bold text-center text-slate-800 mb-10">Kenapa Sewain?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['🗓️', 'Kalender Transparan', 'Lihat ketersediaan barang secara real-time lewat kalender interaktif.'],
                ['⏱️', 'Durasi Fleksibel', 'Sewa per jam, harian, atau bulanan sesuai kebutuhan kamu.'],
                ['🚚', 'Pickup atau Delivery', 'Ambil sendiri atau pilih pengiriman ke alamatmu.'],
                ['⭐', 'Rating Terpercaya', 'Sistem rating dua arah memastikan transaksi aman untuk semua.'],
            ] as [$icon, $title, $desc])
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 hover:shadow-md hover:bg-white transition">
                    <div class="text-3xl mb-3">{{ $icon }}</div>
                    <h3 class="font-semibold text-slate-800 mb-1">{{ $title }}</h3>
                    <p class="text-sm text-slate-500">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Vendor --}}
<section class="bg-slate-800 text-white">
    <div class="max-w-3xl mx-auto px-4 py-14 text-center">
        <h2 class="text-2xl font-bold mb-3">Punya barang yang jarang dipakai?</h2>
        <p class="text-slate-300 mb-6">Daftarkan sebagai vendor dan mulai dapatkan penghasilan dari barang milikmu.</p>
        <a href="{{ route('vendor.register') }}"
           class="inline-block bg-green-500 text-white font-semibold px-8 py-3 rounded-xl hover:bg-green-600 transition text-sm">
            Mulai Jadi Vendor
        </a>
    </div>
</section>
@endsection
