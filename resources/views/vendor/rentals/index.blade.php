@extends('layouts.vendor')
@section('title', 'Semua Rental')

@section('content')

@if($rentals->isEmpty())
    <div class="bg-white border border-slate-200 rounded-2xl p-16 text-center text-slate-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="font-medium">Belum ada rental masuk.</p>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Barang</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Penyewa</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Mulai</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Durasi</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Status</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($rentals as $rental)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $rental->item->name }}</td>
                    <td class="px-5 py-3">
                        <div>
                            <p class="text-slate-700 font-medium">{{ $rental->user->name }}</p>
                            <div class="flex items-center gap-1">
                                <span class="text-yellow-400 text-xs">★</span>
                                <span class="text-xs text-slate-400">{{ number_format($rental->user->rating, 1) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-slate-500">{{ $rental->start->translatedFormat('d M Y') }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ $rental->duration }} {{ $rental->unit }}</td>
                    <td class="px-5 py-3">
                        @if(!$rental->isActive())
                            <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">Selesai</span>
                        @elseif($rental->returning && !$rental->returning->verified)
                            <span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Perlu Verifikasi</span>
                        @elseif($rental->isOverdue())
                            <span class="text-xs font-semibold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Terlambat</span>
                        @else
                            <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Aktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('vendor.rentals.show', $rental) }}"
                           class="text-green-600 hover:underline text-xs font-medium">Detail →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $rentals->links() }}</div>
@endif

@endsection
