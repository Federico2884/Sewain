@extends('layouts.app')
@section('title', 'Booking: '.$item->name)

@push('styles')
<style>
.calendar-day              { cursor: pointer; transition: background 0.15s; }
.calendar-day:hover        { background: #f1f5f9; }
.calendar-day.booked       { background: #fee2e2; color: #ef4444; cursor: not-allowed; }
.calendar-day.booked:hover { background: #fee2e2; }
.calendar-day.past         { color: #cbd5e1; cursor: not-allowed; }
.calendar-day.past:hover   { background: transparent; }
.calendar-day.today        { background: #eff6ff; font-weight: 700; border: 2px solid #3b82f6; }
.calendar-day.selected     { background: #16a34a; color: #fff; font-weight: 700; }
.calendar-day.in-range     { background: #dcfce7; color: #166534; }
.calendar-day.range-conflict { background: #fecaca; color: #7f1d1d; }
.calendar-day.other-month  { color: #cbd5e1; cursor: default; }
.calendar-day.other-month:hover { background: transparent; }
[x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8" x-data="bookingForm()">
    <a href="{{ route('items.show', $item) }}" class="text-sm text-green-600 hover:underline flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Detail Barang
    </a>

    <h1 class="text-xl font-bold text-slate-800 mb-1">Form Booking</h1>
    <p class="text-slate-500 text-sm mb-6">Barang: <span class="font-medium text-slate-700">{{ $item->name }}</span></p>

    <form method="POST" action="{{ route('user.rentals.store', $item) }}" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @csrf

        {{-- Kalender pilih tanggal --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <button type="button" @click="prevMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span class="text-sm font-semibold text-slate-700" x-text="monthYear()"></span>
                <button type="button" @click="nextMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition">
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
                            'in-range': day.inRange && !day.selected && !day.conflict,
                            'range-conflict': day.conflict,
                            'other-month': !day.currentMonth,
                         }"
                         @click="selectDate(day)"
                         x-text="day.day"></div>
                </template>
            </div>
            <div class="flex flex-wrap items-center gap-3 mt-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-100 inline-block"></span> Sudah disewa</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-600 inline-block"></span> Mulai</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-100 inline-block"></span> Rentang sewa</span>
            </div>
            <p class="text-xs text-slate-500 mt-3">
                Klik tanggal hijau yang tersedia untuk menentukan hari mulai sewa.
            </p>
        </div>

        {{-- Form --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            {{-- Tanggal Mulai --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Mulai Sewa</label>
                <input type="date" name="start" x-model="start"
                       min="{{ now()->toDateString() }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('start') border-red-400 @enderror"
                       required>
                @error('start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p x-show="conflict" x-cloak class="text-red-500 text-xs mt-1">
                    Rentang ini tabrakan dengan jadwal sewa lain — pilih tanggal/durasi lain.
                </p>
            </div>

            {{-- Durasi + Unit --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Durasi Sewa</label>
                <div class="flex gap-2">
                    <input type="number" name="duration" x-model.number="duration"
                           min="1" value="{{ old('duration', 1) }}" required
                           class="w-28 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('duration') border-red-400 @enderror">
                    <select name="unit" x-model="unit"
                            class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('unit') border-red-400 @enderror">
                        <option value="jam">Jam</option>
                        <option value="hari">Hari</option>
                        <option value="bulan">Bulan</option>
                    </select>
                </div>
                @error('duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Metode --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Metode Pengambilan</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 border rounded-xl p-3 cursor-pointer transition"
                           :class="method === 'pickup' ? 'border-green-500 bg-green-50' : 'border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="method" value="pickup" x-model="method" class="sr-only">
                        <span class="text-xl">🏪</span>
                        <div>
                            <p class="text-sm font-medium text-slate-800">Pickup</p>
                            <p class="text-xs text-slate-400">Ambil sendiri</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 border rounded-xl p-3 cursor-pointer transition"
                           :class="method === 'delivery' ? 'border-green-500 bg-green-50' : 'border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="method" value="delivery" x-model="method" class="sr-only">
                        <span class="text-xl">🚚</span>
                        <div>
                            <p class="text-sm font-medium text-slate-800">Delivery</p>
                            <p class="text-xs text-slate-400">Diantar ke alamat</p>
                        </div>
                    </label>
                </div>
                @error('method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Summary --}}
            <div class="bg-slate-50 rounded-xl p-4 mb-5 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Harga sewa</span>
                    <span>Rp {{ number_format($item->price, 0, ',', '.') }} × <span x-text="duration"></span> <span x-text="unit"></span></span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Deposit (×{{ Auth::user()->depositMultiplier() }})</span>
                    <span>Rp {{ number_format($deposit, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-slate-200 pt-2 flex justify-between font-semibold text-slate-800">
                    <span>Total Pembayaran</span>
                    <span x-text="'Rp ' + formatRupiah(total())"></span>
                </div>
            </div>

            <button type="submit"
                    :disabled="conflict"
                    :class="conflict ? 'bg-slate-300 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                    class="w-full text-white font-semibold py-3 rounded-xl transition text-sm">
                Lanjut ke Pembayaran
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function bookingForm() {
    const initialStart = @json(old('start', $defaultStart));
    const initialDate  = new Date(initialStart + 'T00:00:00');
    return {
        bookedDates: @json($bookedDates),
        start: initialStart,
        duration: @json((int) old('duration', 1)),
        unit: @json(old('unit', 'hari')),
        method: @json(old('method', 'pickup')),
        pricePerUnit: {{ (float) $item->price }},
        deposit: {{ (float) $deposit }},
        year: initialDate.getFullYear(),
        month: initialDate.getMonth(),
        prevMonth() { if (this.month === 0) { this.month = 11; this.year--; } else { this.month--; } },
        nextMonth() { if (this.month === 11) { this.month = 0; this.year++; } else { this.month++; } },
        monthYear() {
            return new Date(this.year, this.month).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        },
        todayIso() {
            const t = new Date();
            return `${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,'0')}-${String(t.getDate()).padStart(2,'0')}`;
        },
        addDaysIso(iso, amount) {
            const [y, m, d] = iso.split('-').map(Number);
            const dt = new Date(y, m - 1, d);
            dt.setDate(dt.getDate() + amount);
            return `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`;
        },
        rangeDates() {
            if (!this.start || !this.duration) {
                return [];
            }
            const days = this.unit === 'bulan' ? this.duration * 30
                       : this.unit === 'jam'   ? Math.max(1, Math.ceil(this.duration / 24))
                       : this.duration;
            const dates = [];
            for (let i = 0; i <= days; i++) {
                dates.push(this.addDaysIso(this.start, i));
            }
            return dates;
        },
        get conflict() {
            return this.rangeDates().some(d => this.bookedDates.includes(d));
        },
        selectDate(day) {
            if (!day.currentMonth || day.booked || day.past) {
                return;
            }
            this.start = day.date;
        },
        total() {
            return this.pricePerUnit * this.duration + this.deposit;
        },
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(Math.round(num));
        },
        calendarDays() {
            const days = [];
            const first = new Date(this.year, this.month, 1);
            const last  = new Date(this.year, this.month + 1, 0);
            const today = this.todayIso();
            const range = this.rangeDates();
            const conflict = this.conflict;
            const push = (day, date, currentMonth) => {
                const inRange = range.includes(date);
                days.push({
                    day, date, currentMonth,
                    booked: this.bookedDates.includes(date),
                    today: date === today,
                    past: date < today,
                    selected: date === this.start,
                    inRange,
                    conflict: inRange && conflict,
                });
            };
            for (let i = 0; i < first.getDay(); i++) {
                const d = new Date(this.year, this.month, -first.getDay() + i + 1);
                push(d.getDate(), `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`, false);
            }
            for (let i = 1; i <= last.getDate(); i++) {
                const date = `${this.year}-${String(this.month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
                push(i, date, true);
            }
            const remaining = 42 - days.length;
            for (let i = 1; i <= remaining; i++) {
                const d = new Date(this.year, this.month + 1, i);
                push(d.getDate(), `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`, false);
            }
            return days;
        }
    }
}
</script>
@endpush
@endsection
