@extends('layouts.vendor')
@section('title', 'Dashboard')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Barang',   'value' => $vendor->items->count(),            'color' => 'blue'],
        ['label' => 'Sedang Disewa',  'value' => $activeRentals->count(),             'color' => 'green'],
        ['label' => 'Perlu Verifikasi','value' => $pendingReturns->count(),           'color' => 'amber'],
        ['label' => 'Rating Vendor',  'value' => number_format($vendor->rating, 1).' ★', 'color' => 'yellow'],
    ] as $stat)
    <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <p class="text-xs text-slate-400 font-medium mb-1">{{ $stat['label'] }}</p>
        <p class="text-2xl font-bold text-slate-800">{{ $stat['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Pending Returns — most urgent --}}
@if($pendingReturns->count())
<section class="mb-8">
    <h2 class="text-base font-semibold text-slate-800 mb-3 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse inline-block"></span>
        Perlu Verifikasi Pengembalian ({{ $pendingReturns->count() }})
    </h2>
    <div class="bg-white border border-amber-200 rounded-2xl overflow-x-auto">
        <table class="w-full min-w-[560px] text-sm">
            <thead class="bg-amber-50 border-b border-amber-100">
                <tr>
                    <th class="text-left px-5 py-3 text-amber-700 font-medium">Barang</th>
                    <th class="text-left px-5 py-3 text-amber-700 font-medium">Penyewa</th>
                    <th class="text-left px-5 py-3 text-amber-700 font-medium">Metode Kembali</th>
                    <th class="text-left px-5 py-3 text-amber-700 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($pendingReturns as $rental)
                <tr>
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $rental->item->name }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ $rental->user->name }}</td>
                    <td class="px-5 py-3 text-slate-500 capitalize">{{ $rental->returning->method }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('vendor.rentals.show', $rental) }}"
                           class="text-amber-600 hover:underline text-xs font-medium">Verifikasi →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif

{{-- Active Rentals --}}
<section class="mb-8">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-semibold text-slate-800">Sedang Berlangsung ({{ $activeRentals->count() }})</h2>
        <a href="{{ route('vendor.rentals.index') }}" class="text-sm text-green-600 hover:underline">Lihat semua →</a>
    </div>

    @if($activeRentals->isEmpty())
        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-400 text-sm">
            Tidak ada rental aktif saat ini.
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl overflow-x-auto">
            <table class="w-full min-w-[560px] text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Barang</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Penyewa</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Jatuh Tempo</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Sisa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($activeRentals->take(8) as $rental)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $rental->item->name }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $rental->user->name }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $rental->expectedReturnDate()->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @php $days = $rental->daysRemaining(); @endphp
                            <span class="text-xs font-medium
                                {{ $days < 0 ? 'text-red-600' : ($days <= 2 ? 'text-amber-600' : 'text-green-600') }}">
                                {{ $days < 0 ? abs($days).' hari terlambat' : $days.' hari lagi' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

{{-- Quick Links --}}
<section>
    <h2 class="text-base font-semibold text-slate-800 mb-3">Aksi Cepat</h2>
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('vendor.items.create') }}"
           class="bg-green-600 text-white rounded-2xl p-5 hover:bg-green-700 transition flex items-center gap-3">
            <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <div>
                <p class="font-semibold text-sm">Tambah Barang</p>
                <p class="text-xs text-green-200">Daftarkan barang baru</p>
            </div>
        </a>
        <a href="{{ route('vendor.items.index') }}"
           class="bg-white border border-slate-200 rounded-2xl p-5 hover:bg-slate-50 transition flex items-center gap-3">
            <svg class="w-6 h-6 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <div>
                <p class="font-semibold text-sm text-slate-800">Kelola Barang</p>
                <p class="text-xs text-slate-400">{{ $vendor->items->count() }} barang terdaftar</p>
            </div>
        </a>
    </div>
</section>

@endsection
