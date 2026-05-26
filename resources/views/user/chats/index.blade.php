@extends('layouts.app')
@section('title', 'Chat')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Chat dengan Vendor</h1>

    @if($conversations->isEmpty())
        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <p class="text-sm text-slate-500">Belum ada percakapan.</p>
            <p class="text-xs text-slate-400 mt-1">Mulai chat dari halaman detail barang.</p>
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl divide-y divide-slate-100 overflow-hidden">
            @foreach($conversations as $conv)
                @php $unread = $conv->unreadCountFor('user'); @endphp
                <a href="{{ route('user.chats.show', $conv) }}"
                   class="flex items-center gap-4 p-4 hover:bg-slate-50 transition">
                    <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-semibold shrink-0">
                        {{ strtoupper(substr($conv->vendor->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-slate-800 truncate">{{ $conv->vendor->name }}</p>
                            @if($conv->latestMessage)
                                <span class="text-xs text-slate-400 shrink-0">{{ $conv->latestMessage->created_at->diffForHumans(short: true) }}</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between gap-2 mt-0.5">
                            <p class="text-sm text-slate-500 truncate {{ $unread > 0 ? 'font-semibold text-slate-700' : '' }}">
                                {{ $conv->latestMessage?->body ?? 'Belum ada pesan' }}
                            </p>
                            @if($unread > 0)
                                <span class="bg-green-600 text-white text-xs font-semibold px-2 py-0.5 rounded-full shrink-0">{{ $unread }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
