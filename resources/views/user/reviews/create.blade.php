@extends('layouts.app')
@section('title', 'Beri Rating Vendor')

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 py-8">
    <a href="{{ route('user.rentals.show', $rental) }}" class="text-sm text-green-600 hover:underline flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <h1 class="text-xl font-bold text-slate-800 mb-1">Beri Rating Vendor</h1>
    <p class="text-slate-500 text-sm mb-6">
        Bagaimana pengalaman kamu menyewa <span class="font-medium text-slate-700">{{ $rental->item->name }}</span>
        dari <span class="font-medium text-slate-700">{{ $rental->vendor->name }}</span>?
    </p>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <form method="POST" action="{{ route('user.rentals.review.store', $rental) }}" x-data="{ rating: 0, hovered: 0 }">
            @csrf

            {{-- Stars --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-3">Rating</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            @click="rating = {{ $i }}"
                            @mouseenter="hovered = {{ $i }}"
                            @mouseleave="hovered = 0"
                            class="text-4xl transition"
                            :class="(hovered || rating) >= {{ $i }} ? 'text-yellow-400' : 'text-slate-200'">★</button>
                    @endfor
                </div>
                <input type="hidden" name="rating" :value="rating">
                <p class="text-xs text-slate-400 mt-1.5" x-text="['','Sangat Buruk','Buruk','Cukup','Bagus','Sangat Bagus'][rating] || 'Pilih rating'"></p>
                @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Comment --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Komentar <span class="text-slate-400">(opsional)</span></label>
                <textarea name="comment" rows="3" maxlength="500"
                          placeholder="Ceritakan pengalaman kamu..."
                          class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500 @error('comment') border-red-400 @enderror">{{ old('comment') }}</textarea>
                @error('comment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    :disabled="rating === 0"
                    :class="rating === 0 ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-green-600 hover:bg-green-700 text-white'"
                    class="w-full font-semibold py-3 rounded-xl transition text-sm">
                Kirim Rating
            </button>
        </form>
    </div>
</div>
@endsection
