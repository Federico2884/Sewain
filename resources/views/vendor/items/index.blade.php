@extends('layouts.vendor')
@section('title', 'Barang Saya')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-500">{{ $items->total() }} barang terdaftar</p>
    <a href="{{ route('vendor.items.create') }}"
       class="bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Barang
    </a>
</div>

@if($items->isEmpty())
    <div class="bg-white border border-slate-200 rounded-2xl p-16 text-center text-slate-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <p class="font-medium">Belum ada barang.</p>
        <a href="{{ route('vendor.items.create') }}" class="mt-3 inline-block text-sm text-green-600 hover:underline">Tambah barang pertama →</a>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-2xl overflow-x-auto">
        <table class="w-full min-w-[720px] text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Barang</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Kategori</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Harga/Hari</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Deposit</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Status</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($items as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                                 class="w-10 h-10 object-cover rounded-lg bg-slate-100">
                            <span class="font-medium text-slate-800">{{ $item->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-slate-500">{{ $item->category }}</td>
                    <td class="px-5 py-3 text-slate-700">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-slate-700">Rp {{ number_format($item->deposit, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <form method="POST" action="{{ route('vendor.items.toggle-availability', $item) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-xs font-semibold px-2 py-0.5 rounded-full transition
                                        {{ $item->availability ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-600 hover:bg-red-200' }}">
                                {{ $item->availability ? '✓ Tersedia' : '✗ Tidak Tersedia' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('vendor.items.edit', $item) }}" class="text-green-600 hover:underline text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('vendor.items.destroy', $item) }}"
                                  onsubmit="return confirm('Hapus barang ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $items->links() }}</div>
@endif

@endsection
