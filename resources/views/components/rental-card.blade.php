@php
    $days      = $rental->daysRemaining();
    $isOverdue = $rental->isOverdue();
    $isDueSoon = $rental->isDueSoon(2);
    $isPending = $rental->isPending();
    $isFailed  = $rental->isFailed();
    $isDone    = $rental->returning && $rental->returning->verified;
@endphp

<div class="bg-white border rounded-2xl overflow-hidden
    @if($isFailed) border-slate-300 opacity-75
    @elseif($isPending) border-amber-300
    @elseif($isOverdue) border-red-300
    @elseif($isDueSoon) border-amber-300
    @else border-slate-200 @endif">

    <div class="p-4">
        {{-- Status badge --}}
        @if($isFailed)
            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full mb-2">
                ✗ Dibatalkan
            </span>
        @elseif($isPending)
            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full mb-2">
                ⏳ Menunggu Pembayaran
            </span>
        @elseif($isDone)
            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full mb-2">
                ✓ Selesai
            </span>
        @elseif($isOverdue)
            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-red-100 text-red-700 px-2 py-0.5 rounded-full mb-2">
                ⚠️ Terlambat
            </span>
        @elseif($isDueSoon)
            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full mb-2">
                ⏰ Jatuh Tempo {{ $days }} hari lagi
            </span>
        @else
            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full mb-2">
                ✓ Aktif
            </span>
        @endif

        <h3 class="font-semibold text-slate-800 line-clamp-1">{{ $rental->item->name }}</h3>
        <p class="text-xs text-slate-400 mt-0.5">{{ $rental->vendor->name }}</p>

        <div class="mt-3 space-y-1 text-xs text-slate-600">
            <div class="flex justify-between">
                <span class="text-slate-400">Mulai</span>
                <span>{{ $rental->start->translatedFormat('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Kembali</span>
                <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">
                    {{ $rental->expectedReturnDate()->translatedFormat('d M Y') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Durasi</span>
                <span>{{ $rental->duration }} {{ $rental->unit }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Metode</span>
                <span class="capitalize">{{ $rental->method }}</span>
            </div>
        </div>
    </div>

    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50 flex gap-2">
        <a href="{{ route('user.rentals.show', $rental) }}"
           class="flex-1 text-center text-xs font-medium text-slate-600 hover:text-green-600 transition">
            Detail
        </a>
        @if($isPending)
            <form method="POST" action="{{ route('user.rentals.continue-payment', $rental) }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full text-xs font-medium text-amber-600 hover:text-amber-800 transition">
                    Bayar Sekarang
                </button>
            </form>
        @elseif(! $isFailed && ! $rental->returning)
            <form method="POST" action="{{ route('user.rentals.return', $rental) }}" class="flex-1">
                @csrf
                <input type="hidden" name="method" value="{{ $rental->method }}">
                <button type="submit"
                        class="w-full text-xs font-medium text-green-600 hover:text-green-800 transition"
                        onclick="return confirm('Ajukan pengembalian barang?')">
                    Kembalikan
                </button>
            </form>
        @elseif(! $isFailed && ! $rental->returning->verified)
            <span class="flex-1 text-center text-xs text-slate-400">Menunggu verifikasi...</span>
        @endif
    </div>
</div>
