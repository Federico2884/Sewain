@extends('layouts.app')
@section('title', 'Katalog Barang')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header + Search --}}
    <div class="flex flex-col sm:flex-row sm:items-end gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Katalog Barang</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $items->total() }} barang tersedia</p>
        </div>
        <form method="GET" action="{{ route('items.index') }}" class="flex gap-2 sm:ml-auto w-full sm:w-auto">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="flex items-center gap-2 flex-1 border border-slate-300 rounded-lg px-3 focus-within:ring-2 focus-within:ring-green-500 focus-within:border-green-500">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari barang..."
                       class="py-2 text-sm w-full sm:w-52 focus:outline-none bg-transparent">
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition shrink-0">
                Cari
            </button>
        </form>
    </div>

    {{-- Category chips --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-8 -mx-1 px-1">
        <a href="{{ route('items.index', ['search' => request('search')]) }}"
           @class([
               'shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium border transition',
               'bg-green-600 text-white border-green-600' => ! request('category'),
               'bg-white text-slate-600 border-slate-200 hover:border-green-300 hover:text-green-700' => request('category'),
           ])>
            Semua
        </a>
        @foreach($categories as $cat)
            @php($isActive = strcasecmp((string) request('category'), (string) $cat) === 0)
            <a href="{{ route('items.index', ['category' => $cat, 'search' => request('search')]) }}"
               @class([
                   'shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium border transition',
                   'bg-green-600 text-white border-green-600' => $isActive,
                   'bg-white text-slate-600 border-slate-200 hover:border-green-300 hover:text-green-700' => ! $isActive,
               ])>
                <span>{{ \App\Models\Item::categoryIcon($cat) }}</span>
                {{ $cat }}
            </a>
        @endforeach
    </div>

    {{-- Grid --}}
    @if($items->isEmpty())
        <div class="text-center py-20 text-slate-400 bg-white border border-dashed border-slate-200 rounded-2xl">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg>
            <p class="font-medium text-slate-500">Belum ada barang di kategori ini.</p>
            <p class="text-sm text-slate-400 mt-1">Coba kategori lain atau ubah kata kunci pencarian.</p>
            @if(request()->hasAny(['search','category']))
                <a href="{{ route('items.index') }}" class="inline-block mt-4 text-sm font-medium text-green-600 hover:text-green-700">Reset filter</a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($items as $item)
            <a href="{{ route('items.show', $item) }}"
               class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition group">
                <div class="aspect-video bg-slate-100 overflow-hidden">
                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                         class="w-full h-full object-contain group-hover:scale-105 transition duration-300">
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
