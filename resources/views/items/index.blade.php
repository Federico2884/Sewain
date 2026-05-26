@extends('layouts.app')
@section('title', 'Katalog Barang')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header + Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-end gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Katalog Barang</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $items->total() }} barang tersedia</p>
        </div>
        <form method="GET" action="{{ route('items.index') }}" class="flex gap-2 sm:ml-auto">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari barang..."
                   class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-green-500">
            <select name="category"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                Cari
            </button>
            @if(request()->hasAny(['search','category']))
                <a href="{{ route('items.index') }}" class="border border-slate-300 text-slate-600 px-4 py-2 rounded-lg text-sm hover:bg-slate-50 transition">Reset</a>
            @endif
        </form>
    </div>

    {{-- Grid --}}
    @if($items->isEmpty())
        <div class="text-center py-20 text-slate-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg>
            <p class="font-medium">Tidak ada barang ditemukan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($items as $item)
            <a href="{{ route('items.show', $item) }}"
               class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition group">
                <div class="aspect-video bg-slate-100 overflow-hidden">
                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                <div class="p-4">
                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">{{ $item->category }}</span>
                    <h3 class="font-semibold text-slate-800 mt-2 mb-1 line-clamp-1">{{ $item->name }}</h3>
                    <p class="text-xs text-slate-400 mb-3">oleh {{ $item->vendor->name }}</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">/hari</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-yellow-400 text-sm">★</span>
                            <span class="text-xs font-medium text-slate-600">{{ number_format($item->vendor->rating, 1) }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $items->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
