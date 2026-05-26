@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
{{-- Hero --}}
<section class="bg-gradient-to-br from-green-600 to-green-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold mb-4 leading-tight">
            Sewa Barang Lebih Mudah,<br>Lebih Terpercaya
        </h1>
        <p class="text-green-100 text-lg mb-8 max-w-xl mx-auto">
            Temukan ribuan barang dari vendor terpercaya. Pilih jadwal, durasi, dan metode pengiriman sesuai kebutuhan.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('items.index') }}"
               class="bg-white text-green-700 font-semibold px-8 py-3 rounded-xl hover:bg-green-50 transition text-sm">
                Cari Barang Sekarang
            </a>
            <a href="{{ route('vendor.register') }}"
               class="border border-white/40 text-white font-semibold px-8 py-3 rounded-xl hover:bg-white/10 transition text-sm">
                Daftar sebagai Vendor
            </a>
        </div>
    </div>
</section>

{{-- Features --}}
<!-- <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-2xl font-bold text-center mb-10">Kenapa Sewain?</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['🗓️', 'Kalender Transparan', 'Lihat ketersediaan barang secara real-time lewat kalender interaktif.'],
            ['⏱️', 'Durasi Fleksibel', 'Sewa per jam, harian, atau bulanan sesuai kebutuhan kamu.'],
            ['🚚', 'Pickup atau Delivery', 'Ambil sendiri atau pilih pengiriman ke alamatmu.'],
            ['⭐', 'Rating Terpercaya', 'Sistem rating dua arah memastikan transaksi aman untuk semua.'],
        ] as [$icon, $title, $desc])
        <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center hover:shadow-md transition">
            <div class="text-4xl mb-3">{{ $icon }}</div>
            <h3 class="font-semibold text-slate-800 mb-1">{{ $title }}</h3>
            <p class="text-sm text-slate-500">{{ $desc }}</p>
        </div>
        @endforeach
    </div>
</section> -->

{{-- CTA Vendor --}}
<section class="bg-slate-800 text-white py-14">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold mb-3">Punya barang yang jarang dipakai?</h2>
        <p class="text-slate-300 mb-6">Daftarkan sebagai vendor dan mulai dapatkan penghasilan dari barang milikmu.</p>
        <a href="{{ route('vendor.register') }}"
           class="bg-green-500 text-white font-semibold px-8 py-3 rounded-xl hover:bg-green-600 transition text-sm">
            Mulai Jadi Vendor
        </a>
    </div>
</section>
@endsection
