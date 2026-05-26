@extends('layouts.vendor')
@section('title', 'Edit Barang')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('vendor.items.index') }}" class="text-sm text-green-600 hover:underline flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Barang
    </a>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('vendor.items.update', $item) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Barang</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-400 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                    <input type="text" name="category" value="{{ old('category', $item->category) }}" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('category') border-red-400 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                    <select name="availability"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="1" {{ old('availability', $item->availability) ? 'selected' : '' }}>Tersedia</option>
                        <option value="0" {{ !old('availability', $item->availability) ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Harga Sewa / Hari (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $item->price) }}" min="0" step="500" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('price') border-red-400 @enderror">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deposit (Rp)</label>
                    <input type="number" name="deposit" value="{{ old('deposit', $item->deposit) }}" min="0" step="500" required
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('deposit') border-red-400 @enderror">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Foto Barang</label>
                    {{-- Current image preview --}}
                    <div class="mb-2 flex items-center gap-3">
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                             class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                        <span class="text-xs text-slate-400">Foto saat ini. Upload baru untuk mengganti.</span>
                    </div>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:border-0 file:bg-green-50 file:text-green-700 file:font-medium file:text-sm focus:outline-none @error('image') border-red-400 @enderror">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-green-600 text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-green-700 transition text-sm">
                    Simpan Perubahan
                </button>
                <a href="{{ route('vendor.items.index') }}"
                   class="border border-slate-300 text-slate-600 font-medium px-6 py-2.5 rounded-xl hover:bg-slate-50 transition text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
