@extends('layouts.vendor')
@section('title', 'Detail Rental')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('vendor.rentals.index') }}" class="text-sm text-green-600 hover:underline flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Rental
    </a>

    {{-- Rental Info --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-5">
        <div class="flex gap-4 mb-4">
            <img src="{{ Storage::url($rental->item->image) }}" alt="{{ $rental->item->name }}"
                 class="w-20 h-20 object-cover rounded-xl bg-slate-100 shrink-0">
            <div>
                <h2 class="font-bold text-slate-800 text-lg">{{ $rental->item->name }}</h2>
                <p class="text-sm text-slate-500">{{ $rental->item->category }}</p>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-4 grid grid-cols-2 gap-3 text-sm">
            <div><p class="text-slate-400 text-xs mb-0.5">Penyewa</p><p class="font-medium">{{ $rental->user->name }}</p></div>
            <div>
                <p class="text-slate-400 text-xs mb-0.5">Rating Penyewa</p>
                <div class="flex items-center gap-1">
                    <span class="text-yellow-400">★</span>
                    <span class="font-medium">{{ number_format($rental->user->rating, 1) }}</span>
                    <span class="text-slate-400 text-xs">({{ $rental->user->reviewer_count }} ulasan)</span>
                </div>
            </div>
            <div><p class="text-slate-400 text-xs mb-0.5">Tanggal Mulai</p><p class="font-medium">{{ $rental->start->translatedFormat('d M Y') }}</p></div>
            <div><p class="text-slate-400 text-xs mb-0.5">Jatuh Tempo</p><p class="font-medium">{{ $rental->expectedReturnDate()->translatedFormat('d M Y') }}</p></div>
            <div><p class="text-slate-400 text-xs mb-0.5">Durasi</p><p class="font-medium">{{ $rental->duration }} {{ $rental->unit }}</p></div>
            <div><p class="text-slate-400 text-xs mb-0.5">Metode</p><p class="font-medium capitalize">{{ $rental->method }}</p></div>
        </div>

        <div class="border-t border-slate-100 mt-4 pt-4 flex items-center justify-between">
            <div class="text-sm">
                <span class="text-slate-400">Biaya Sewa + Deposit:</span>
                <span class="font-bold text-slate-800 ml-2">Rp {{ number_format($rental->grandTotal(), 0, ',', '.') }}</span>
            </div>
            @php $days = $rental->daysRemaining(); @endphp
            <span class="text-xs font-semibold px-2 py-1 rounded-full
                {{ $rental->isOverdue() ? 'bg-red-100 text-red-700' : ($rental->isDueSoon() ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                {{ $rental->isOverdue() ? abs($days).' hari terlambat' : $days.' hari lagi' }}
            </span>
        </div>
    </div>

    {{-- Payment Info --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800">Pembayaran</h3>
            @if($rental->payment)
                <span class="text-xs font-semibold px-3 py-1 rounded-full
                    @if($rental->payment->isPaid()) bg-green-50 text-green-700
                    @elseif($rental->payment->isFailed()) bg-red-50 text-red-700
                    @else bg-slate-100 text-slate-700 @endif">
                    {{ $rental->payment->statusLabel() }}
                </span>
            @endif
        </div>

        @if(!$rental->payment)
            <p class="text-sm text-slate-400 italic">Belum ada pembayaran.</p>
        @else
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-slate-400 text-xs mb-0.5">Total</p><p class="font-medium">Rp {{ number_format($rental->payment->amount, 0, ',', '.') }}</p></div>
                <div><p class="text-slate-400 text-xs mb-0.5">Metode</p><p class="font-medium">{{ $rental->payment->methodLabel() ?? '—' }}</p></div>
                @if($rental->payment->transaction_id)
                    <div class="col-span-2"><p class="text-slate-400 text-xs mb-0.5">ID Transaksi</p><p class="font-mono text-xs">{{ $rental->payment->transaction_id }}</p></div>
                @endif
                @if($rental->payment->paid_at)
                    <div class="col-span-2"><p class="text-slate-400 text-xs mb-0.5">Dibayar pada</p><p class="font-medium">{{ $rental->payment->paid_at->translatedFormat('d M Y H:i') }}</p></div>
                @endif
            </div>
        @endif
    </div>

    {{-- Return Verification --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-5">
        <h3 class="font-semibold text-slate-800 mb-4">Pengembalian</h3>

        @if(!$rental->returning)
            <p class="text-sm text-slate-400 italic">Penyewa belum mengajukan pengembalian.</p>

        @elseif($rental->returning->verified)
            <div class="flex items-center gap-3 text-sm">
                <span class="text-2xl">{{ $rental->returning->isGoodCondition() ? '✅' : '⚠️' }}</span>
                <div>
                    <p class="font-semibold text-slate-800">
                        {{ $rental->returning->isGoodCondition() ? 'Kondisi Baik' : 'Ada Kerusakan' }}
                    </p>
                    <p class="text-slate-400 text-xs">Sudah diverifikasi</p>
                </div>
            </div>

        @else
            {{-- Awaiting verification --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4 text-sm">
                <p class="font-medium text-amber-800 mb-1">⏳ Menunggu Verifikasi</p>
                <p class="text-amber-600 text-xs">
                    Penyewa mengajukan pengembalian melalui: <strong>{{ ucfirst($rental->returning->method) }}</strong>
                </p>
            </div>

            <form method="POST" action="{{ route('vendor.rentals.return.verify', $rental) }}" x-data="{ status: '' }">
                @csrf
                <p class="text-sm font-medium text-slate-700 mb-3">Bagaimana kondisi barang setelah dikembalikan?</p>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <label class="flex items-center gap-3 border rounded-xl p-3 cursor-pointer transition"
                           :class="status === 'good' ? 'border-green-500 bg-green-50' : 'border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="status" value="good" x-model="status" class="sr-only">
                        <span class="text-2xl">✅</span>
                        <div>
                            <p class="text-sm font-medium text-slate-800">Kondisi Baik</p>
                            <p class="text-xs text-slate-400">Deposit dikembalikan penuh/85%</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 border rounded-xl p-3 cursor-pointer transition"
                           :class="status === 'damaged' ? 'border-red-500 bg-red-50' : 'border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="status" value="damaged" x-model="status" class="sr-only">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <p class="text-sm font-medium text-slate-800">Ada Kerusakan</p>
                            <p class="text-xs text-slate-400">Deposit ditahan penuh</p>
                        </div>
                    </label>
                </div>

                <button type="submit"
                        :disabled="status === ''"
                        :class="status === '' ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-green-600 hover:bg-green-700 text-white'"
                        class="w-full font-semibold py-2.5 rounded-xl transition text-sm"
                        onclick="return confirm('Konfirmasi verifikasi pengembalian?')">
                    Verifikasi Pengembalian
                </button>
            </form>
        @endif
    </div>

    {{-- Review Penyewa --}}
    @if($rental->returning?->verified)
    <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <h3 class="font-semibold text-slate-800 mb-3">Rating Penyewa</h3>
        @if($rental->userReview)
            <div class="flex items-center gap-2">
                <div class="flex text-yellow-400">
                    @for($i = 1; $i <= 5; $i++)
                        <span>{{ $i <= $rental->userReview->rating ? '★' : '☆' }}</span>
                    @endfor
                </div>
                <span class="text-sm font-medium text-slate-700">{{ $rental->userReview->rating }}/5</span>
            </div>
            @if($rental->userReview->comment)
                <p class="text-sm text-slate-500 mt-2 italic">"{{ $rental->userReview->comment }}"</p>
            @endif
        @else
            <a href="{{ route('vendor.rentals.review.create', $rental) }}"
               class="inline-block bg-amber-500 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-amber-600 transition">
                Beri Rating Penyewa
            </a>
        @endif
    </div>
    @endif
</div>
@endsection
