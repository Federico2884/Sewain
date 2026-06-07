@extends('layouts.app')
@section('title', 'Pembayaran')

@section('content')
@php
    use Carbon\Carbon;
    $methods = \App\Models\Payment::methods();
    $start = Carbon::parse($booking['start']);
    $end   = match ($booking['unit']) {
        'jam'   => $start->copy()->addHours((int) $booking['duration']),
        'bulan' => $start->copy()->addMonths((int) $booking['duration']),
        default => $start->copy()->addDays((int) $booking['duration']),
    };
@endphp

<div class="max-w-lg mx-auto px-4 sm:px-6 py-8">
    <a href="{{ route('user.rentals.create', $item) }}"
       class="text-sm text-green-600 hover:underline flex items-center gap-1 mb-4">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Form Booking
    </a>

    <h1 class="text-xl font-bold text-slate-800 mb-6">Pembayaran</h1>

    {{-- Order Summary --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-5">
        <h2 class="font-semibold text-slate-800 mb-4">Ringkasan Order</h2>
        <div class="flex gap-4 mb-4">
            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                 class="w-20 h-20 object-cover rounded-xl bg-slate-100">
            <div>
                <h3 class="font-semibold text-slate-800">{{ $item->name }}</h3>
                <p class="text-sm text-slate-400">{{ $item->vendor->name }}</p>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $start->translatedFormat('d M Y') }} → {{ $end->translatedFormat('d M Y') }}
                </p>
                <p class="text-xs text-slate-500">{{ $booking['duration'] }} {{ $booking['unit'] }} • {{ ucfirst($booking['method']) }}</p>
            </div>
        </div>
        <div class="border-t border-slate-100 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-slate-600">
                <span>Harga sewa</span>
                <span>Rp {{ number_format($rentAmount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Deposit</span>
                <span>Rp {{ number_format($depositAmount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-slate-800 border-t border-slate-100 pt-2">
                <span>Total Pembayaran</span>
                <span class="text-green-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('user.rentals.confirm', $item) }}"
          x-data="{ selected: '' }" class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        @csrf
        <input type="hidden" name="method" :value="selected">

        @foreach(['E-Wallet', 'Bank Transfer'] as $group)
            <div class="px-5 pt-4 pb-2">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ $group }}</p>
            </div>
            <div class="px-3 pb-1 space-y-2">
                @foreach($methods as $key => $meta)
                    @continue($meta['group'] !== $group)
                    <button type="button" @click="selected = '{{ $key }}'"
                            class="w-full flex items-center gap-4 px-3 py-3 rounded-xl border-2 transition text-left"
                            :class="selected === '{{ $key }}'
                                ? 'border-green-500 bg-green-50'
                                : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'">
                        <div class="w-11 h-11 rounded-xl {{ $meta['color'] }} flex items-center justify-center text-white font-bold text-sm shrink-0">
                            {{ strtoupper(substr($meta['label'], 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $meta['label'] }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $group === 'E-Wallet' ? 'Bayar instan via aplikasi' : 'Transfer melalui VA bank' }}
                            </p>
                        </div>
                        <template x-if="selected === '{{ $key }}'">
                            <span class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </template>
                        <template x-if="selected !== '{{ $key }}'">
                            <span class="w-6 h-6 rounded-full border-2 border-slate-300 shrink-0"></span>
                        </template>
                    </button>
                @endforeach
            </div>
        @endforeach

        @error('method')
            <p class="text-red-500 text-xs px-5 py-2">{{ $message }}</p>
        @enderror

        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100 mt-3">
            <div class="flex items-center justify-between mb-3 text-sm">
                <span class="text-slate-500">Total Pembayaran</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <button type="submit"
                    :disabled="!selected"
                    :class="!selected ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-green-600 hover:bg-green-700 text-white'"
                    class="w-full font-semibold py-3 rounded-xl transition text-sm">
                Bayar Sekarang
            </button>
        </div>
    </form>
</div>
@endsection
