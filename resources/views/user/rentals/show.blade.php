@extends('layouts.app')
@section('title', 'Detail Rental')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
    <a href="{{ route('user.rentals.index') }}" class="text-sm text-green-600 hover:underline flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Rental
    </a>

    {{-- Item Info --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-5">
        <div class="flex gap-4">
            <img src="{{ Storage::url($rental->item->image) }}" alt="{{ $rental->item->name }}"
                 class="w-24 h-24 object-cover rounded-xl bg-slate-100">
            <div>
                <h1 class="font-bold text-slate-800 text-lg">{{ $rental->item->name }}</h1>
                <p class="text-sm text-slate-400">{{ $rental->vendor->name }}</p>
                <div class="mt-2 space-y-0.5 text-xs text-slate-600">
                    <p>📅 {{ $rental->start->translatedFormat('d M Y') }} — {{ $rental->expectedReturnDate()->translatedFormat('d M Y') }}</p>
                    <p>⏱ {{ $rental->duration }} {{ $rental->unit }}</p>
                    <p>{{ $rental->method === 'pickup' ? '🏪 Pickup' : '🚚 Delivery' }}</p>
                </div>
            </div>
        </div>
        <div class="mt-4 border-t border-slate-100 pt-4 grid grid-cols-3 gap-4 text-center text-sm">
            <div>
                <p class="text-slate-400 text-xs">Biaya Sewa</p>
                <p class="font-semibold text-slate-800">Rp {{ number_format($rental->totalCost(), 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Deposit</p>
                <p class="font-semibold text-slate-800">Rp {{ number_format($rental->adjustedDeposit(), 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Total</p>
                <p class="font-bold text-green-600">Rp {{ number_format($rental->grandTotal(), 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Payment / Return Status (kondisional berdasarkan status) --}}
    @if($rental->isPending())
        <div class="bg-white border border-amber-200 rounded-2xl p-5 mb-5">
            <h2 class="font-semibold text-slate-800 mb-3">Pembayaran Belum Selesai</h2>
            <p class="text-sm text-slate-500 mb-4">
                Rental ini akan dikonfirmasi setelah pembayaran sebesar
                <span class="font-semibold text-slate-800">Rp {{ number_format($rental->payment->amount, 0, ',', '.') }}</span>
                berhasil diproses.
            </p>
            <form method="POST" action="{{ route('user.rentals.continue-payment', $rental) }}">
                @csrf
                <button type="submit"
                        class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                    Bayar Sekarang
                </button>
            </form>
        </div>

    @elseif($rental->isFailed())
        <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-5">
            <h2 class="font-semibold text-slate-800 mb-2">Rental Dibatalkan</h2>
            <p class="text-sm text-slate-500">
                Rental ini dibatalkan karena tanggal yang dibooking sudah disewa oleh pengguna lain.
            </p>
        </div>

    @else
        <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-5">
            <h2 class="font-semibold text-slate-800 mb-3">Status Pengembalian</h2>
            @if(!$rental->returning)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Belum dikembalikan</span>
                    <form method="POST" action="{{ route('user.rentals.return', $rental) }}">
                        @csrf
                        <input type="hidden" name="method" value="{{ $rental->method }}">
                        <button type="submit"
                                class="bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-700 transition"
                                onclick="return confirm('Ajukan pengembalian?')">
                            Ajukan Pengembalian
                        </button>
                    </form>
                </div>
            @else
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Metode</span>
                        <span class="capitalize font-medium">{{ $rental->returning->method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="capitalize font-medium
                            {{ $rental->returning->status === 'good' ? 'text-green-600' : ($rental->returning->status === 'damaged' ? 'text-red-600' : 'text-amber-600') }}">
                            {{ match($rental->returning->status) {
                                'pending'  => 'Menunggu Konfirmasi',
                                'returned' => 'Sudah Dikembalikan',
                                'good'     => 'Kondisi Baik',
                                'damaged'  => 'Ada Kerusakan',
                                default    => $rental->returning->status
                            } }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Diverifikasi</span>
                        <span class="font-medium {{ $rental->returning->verified ? 'text-green-600' : 'text-amber-600' }}">
                            {{ $rental->returning->verified ? '✓ Ya' : 'Belum' }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Review --}}
    @if($rental->returning?->verified)
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <h2 class="font-semibold text-slate-800 mb-3">Rating Vendor</h2>
            @if($rental->vendorReview)
                <div class="flex items-center gap-2">
                    <div class="flex text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $rental->vendorReview->rating ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <span class="text-sm font-medium text-slate-700">{{ $rental->vendorReview->rating }}/5</span>
                </div>
                @if($rental->vendorReview->comment)
                    <p class="text-sm text-slate-500 mt-2 italic">"{{ $rental->vendorReview->comment }}"</p>
                @endif
            @else
                <a href="{{ route('user.rentals.review.create', $rental) }}"
                   class="inline-block bg-amber-500 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-amber-600 transition">
                    Beri Rating Vendor
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
