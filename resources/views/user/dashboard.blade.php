@extends('layouts.app')
@section('title', 'Dashboard Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Greeting --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center gap-4 sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Halo, {{ $user->name }} 👋</h1>
            <div class="flex items-center gap-3 mt-1">
                <span class="text-yellow-400">★</span>
                <span class="text-sm font-medium text-slate-600">{{ number_format($user->rating, 1) }} rating penyewa</span>
                <span class="text-xs text-slate-400">({{ $user->reviewer_count }} ulasan)</span>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                    {{ $user->depositMultiplier() < 1 ? 'bg-green-100 text-green-700' : ($user->depositMultiplier() > 1 ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600') }}">
                    Deposit {{ $user->depositMultiplier() < 1 ? 'diskon '.(int)((1-$user->depositMultiplier())*100).'%' : ($user->depositMultiplier() > 1 ? 'tambah '.(int)(($user->depositMultiplier()-1)*100).'%' : 'normal') }}
                </span>
            </div>
        </div>
        <a href="{{ route('items.index') }}"
           class="bg-green-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-green-700 transition text-sm self-start">
            + Sewa Barang
        </a>
    </div>

    {{-- Active Rentals --}}
    <section class="mb-10">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Sedang Disewa ({{ $activeRentals->count() }})</h2>
        @if($activeRentals->isEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center text-slate-400">
                <p>Belum ada barang yang sedang kamu sewa.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeRentals as $rental)
                    @include('components.rental-card', ['rental' => $rental])
                @endforeach
            </div>
        @endif
    </section>

    {{-- Completed Rentals --}}
    <section>
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Riwayat Rental ({{ $completedRentals->count() }})</h2>
        @if($completedRentals->isEmpty())
            <p class="text-slate-400 text-sm">Belum ada riwayat rental.</p>
        @else
            <div class="bg-white border border-slate-200 rounded-2xl overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-5 py-3 text-slate-500 font-medium">Barang</th>
                            <th class="text-left px-5 py-3 text-slate-500 font-medium">Vendor</th>
                            <th class="text-left px-5 py-3 text-slate-500 font-medium">Durasi</th>
                            <th class="text-left px-5 py-3 text-slate-500 font-medium">Total</th>
                            <th class="text-left px-5 py-3 text-slate-500 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($completedRentals as $rental)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $rental->item->name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $rental->vendor->name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $rental->duration }} {{ $rental->unit }}</td>
                            <td class="px-5 py-3 text-slate-800">Rp {{ number_format($rental->totalCost(), 0, ',', '.') }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('user.rentals.show', $rental) }}" class="text-green-600 hover:underline text-xs font-medium">Detail</a>
                                @if(!$rental->vendorReview)
                                    <a href="{{ route('user.rentals.review.create', $rental) }}" class="ml-3 text-amber-600 hover:underline text-xs font-medium">Beri Rating</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
