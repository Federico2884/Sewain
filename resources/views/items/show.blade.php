@extends('layouts.app')
@section('title', $item->name)

@push('styles')
<style>
.calendar-day            { cursor: pointer; transition: background 0.15s; }
.calendar-day:hover      { background: #f1f5f9; }
.calendar-day.booked     { background: #fee2e2; color: #ef4444; cursor: not-allowed; }
.calendar-day.booked:hover { background: #fee2e2; }
.calendar-day.past       { color: #cbd5e1; cursor: not-allowed; }
.calendar-day.past:hover { background: transparent; }
.calendar-day.today      { background: #eff6ff; font-weight: 700; border: 2px solid #3b82f6; }
.calendar-day.selected   { background: #16a34a; color: #fff; font-weight: 700; }
.calendar-day.other-month{ color: #cbd5e1; cursor: default; }
.calendar-day.other-month:hover { background: transparent; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('items.index') }}" class="text-sm text-green-600 hover:underline flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Katalog
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Image --}}
        <div>
            <div class="aspect-video bg-slate-100 rounded-2xl overflow-hidden">
                <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
            </div>
        </div>

        {{-- Info --}}
        <div class="flex flex-col gap-4">
            <div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">{{ $item->category }}</span>
                <h1 class="text-2xl font-bold text-slate-800 mt-2">{{ $item->name }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-yellow-400">★</span>
                    <span class="text-sm font-medium text-slate-600">{{ number_format($item->vendor->rating, 1) }}</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-sm text-slate-500">{{ $item->vendor->name }}</span>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Harga Sewa</span>
                    <span class="font-semibold text-slate-800">Rp {{ number_format($item->price, 0, ',', '.') }} / hari</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Deposit</span>
                    <span class="font-semibold text-slate-800">Rp {{ number_format($item->deposit, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Status</span>
                    @if(! $item->availability)
                        <span class="font-semibold text-red-500">✗ Dinonaktifkan vendor</span>
                    @elseif($isRentedNow)
                        <span class="font-semibold text-amber-600">
                            ⏳ Sedang disewa @if($nextAvailableFrom) · siap pada {{ $nextAvailableFrom->translatedFormat('d M Y') }} @endif
                        </span>
                    @else
                        <span class="font-semibold text-green-600">✓ Tersedia</span>
                    @endif
                </div>
            </div>

            {{-- Calendar --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4" x-data="calendar({{ json_encode($bookedDates) }})">
                <div class="flex items-center justify-between mb-3">
                    <button @click="prevMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition">
                        <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span class="text-sm font-semibold text-slate-700" x-text="monthYear()"></span>
                    <button @click="nextMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition">
                        <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="grid grid-cols-7 gap-1 mb-1">
                    <template x-for="d in ['Min','Sen','Sel','Rab','Kam','Jum','Sab']">
                        <div class="text-xs text-slate-400 text-center font-medium py-1" x-text="d"></div>
                    </template>
                </div>
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="day in calendarDays()" :key="day.date">
                        <div class="calendar-day text-center text-xs py-1.5 rounded-lg"
                             :class="{
                                'booked': day.booked,
                                'past': day.past,
                                'today': day.today,
                                'selected': day.selected,
                                'other-month': !day.currentMonth,
                             }"
                             @click="selectDate(day)"
                             x-text="day.day"></div>
                    </template>
                </div>
                <div class="flex flex-wrap items-center gap-3 mt-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-100 inline-block"></span> Sudah disewa</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-50 border-2 border-blue-500 inline-block"></span> Hari ini</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-600 inline-block"></span> Tanggal pilihan</span>
                </div>
                <p class="text-xs text-slate-500 mt-3">
                    Klik tanggal yang tersedia untuk memulai booking di waktu itu.
                </p>

                @if($item->availability)
                    @auth
                        <div class="grid grid-cols-3 gap-3 mt-4">
                            <a :href="bookHref('{{ route('user.rentals.create', $item) }}')"
                               class="col-span-2 block text-center bg-green-600 text-white font-semibold py-3 rounded-xl hover:bg-green-700 transition">
                                <span x-show="!selected">Sewa Sekarang</span>
                                <span x-show="selected" x-cloak>Sewa mulai <span x-text="selectedLabel()"></span></span>
                            </a>
                            <form method="POST" action="{{ route('user.chats.start', $item->vendor) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-white border border-green-600 text-green-700 font-semibold py-3 rounded-xl hover:bg-green-50 transition flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Chat
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="block text-center bg-green-600 text-white font-semibold py-3 rounded-xl hover:bg-green-700 transition mt-4">
                            Masuk untuk Menyewa
                        </a>
                    @endauth
                @else
                    <button disabled class="block w-full text-center bg-slate-300 text-slate-500 font-semibold py-3 rounded-xl cursor-not-allowed mt-4">
                        Sedang Tidak Tersedia
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function calendar(bookedDates) {
    return {
        year: new Date().getFullYear(),
        month: new Date().getMonth(),
        bookedDates,
        selected: null,
        prevMonth() { if (this.month === 0) { this.month = 11; this.year--; } else { this.month--; } },
        nextMonth() { if (this.month === 11) { this.month = 0; this.year++; } else { this.month++; } },
        monthYear() {
            return new Date(this.year, this.month).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        },
        todayIso() {
            const t = new Date();
            return `${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,'0')}-${String(t.getDate()).padStart(2,'0')}`;
        },
        selectDate(day) {
            if (!day.currentMonth || day.booked || day.past) {
                return;
            }
            this.selected = day.date;
        },
        selectedLabel() {
            if (!this.selected) {
                return '';
            }
            const [y, m, d] = this.selected.split('-').map(Number);
            return new Date(y, m - 1, d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        },
        bookHref(base) {
            return this.selected ? `${base}?start=${this.selected}` : base;
        },
        calendarDays() {
            const days = [];
            const first = new Date(this.year, this.month, 1);
            const last  = new Date(this.year, this.month + 1, 0);
            const today = this.todayIso();
            // Pad start
            for (let i = 0; i < first.getDay(); i++) {
                const d = new Date(this.year, this.month, -first.getDay() + i + 1);
                days.push({ day: d.getDate(), date: d.toISOString().slice(0,10), currentMonth: false, booked: false, today: false, past: false, selected: false });
            }
            for (let i = 1; i <= last.getDate(); i++) {
                const date = `${this.year}-${String(this.month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
                days.push({
                    day: i,
                    date,
                    currentMonth: true,
                    booked: this.bookedDates.includes(date),
                    today: date === today,
                    past: date < today,
                    selected: date === this.selected,
                });
            }
            // Pad end to complete 6 rows
            const remaining = 42 - days.length;
            for (let i = 1; i <= remaining; i++) {
                const d = new Date(this.year, this.month + 1, i);
                days.push({ day: d.getDate(), date: d.toISOString().slice(0,10), currentMonth: false, booked: false, today: false, past: false, selected: false });
            }
            return days;
        }
    }
}
</script>
@endpush
@endsection
