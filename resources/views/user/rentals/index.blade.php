@extends('layouts.app')
@section('title', 'Rental Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Rental Saya</h1>
        <a href="{{ route('items.index') }}"
           class="bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-green-700 transition">
            + Sewa Barang
        </a>
    </div>

    @if($rentals->isEmpty())
        <div class="bg-white border border-slate-200 rounded-2xl p-16 text-center text-slate-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="font-medium">Belum ada rental.</p>
            <a href="{{ route('items.index') }}" class="mt-3 inline-block text-sm text-green-600 hover:underline">Mulai sewa sekarang →</a>
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Barang</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Vendor</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Durasi</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Jatuh Tempo</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Status</th>
                        <th class="text-left px-5 py-3 text-slate-500 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rentals as $rental)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3">
                            <span class="font-medium text-slate-800">{{ $rental->item->name }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $rental->vendor->name }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $rental->duration }} {{ $rental->unit }}</td>
                        <td class="px-5 py-3 text-slate-500">
                            {{ $rental->expectedReturnDate()->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-5 py-3">
                            @if($rental->isFailed())
                                <span class="text-xs font-semibold bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full">Dibatalkan</span>
                            @elseif($rental->isPending())
                                <span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Menunggu Pembayaran</span>
                            @elseif($rental->returning && $rental->returning->verified)
                                <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">Selesai</span>
                            @elseif($rental->isOverdue())
                                <span class="text-xs font-semibold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Terlambat</span>
                            @elseif($rental->isDueSoon())
                                <span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Jatuh Tempo</span>
                            @elseif($rental->returning)
                                <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Dikembalikan</span>
                            @else
                                <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Aktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($rental->isPending())
                                <form method="POST" action="{{ route('user.rentals.continue-payment', $rental) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:underline text-xs font-medium">Bayar Sekarang</button>
                                </form>
                                <span class="text-slate-300 mx-1">•</span>
                            @endif
                            <a href="{{ route('user.rentals.show', $rental) }}"
                               class="text-green-600 hover:underline text-xs font-medium">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $rentals->links() }}</div>
    @endif
</div>
@endsection
