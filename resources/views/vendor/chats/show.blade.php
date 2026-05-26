@extends('layouts.vendor')
@section('title', 'Chat — ' . $conversation->user->name)

@section('content')
<div class="max-w-2xl flex flex-col" style="height: calc(100vh - 10rem);">
    {{-- Header --}}
    <div class="bg-white border border-slate-200 rounded-t-2xl px-5 py-4 flex items-center gap-3">
        <a href="{{ route('vendor.chats.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-semibold shrink-0">
            {{ strtoupper(substr($conversation->user->name, 0, 1)) }}
        </div>
        <div>
            <p class="font-semibold text-slate-800">{{ $conversation->user->name }}</p>
            <p class="text-xs text-slate-400">Penyewa</p>
        </div>
    </div>

    {{-- Messages --}}
    <div id="messages" class="flex-1 bg-slate-50 border-x border-slate-200 px-5 py-4 overflow-y-auto space-y-3">
        @forelse($messages as $msg)
            <div class="flex {{ $msg->fromVendor() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[75%] {{ $msg->fromVendor() ? 'bg-green-600 text-white rounded-2xl rounded-br-sm' : 'bg-white text-slate-800 border border-slate-200 rounded-2xl rounded-bl-sm' }} px-4 py-2.5">
                    <p class="text-sm whitespace-pre-wrap break-words">{{ $msg->body }}</p>
                    <p class="text-[10px] mt-1 {{ $msg->fromVendor() ? 'text-green-100' : 'text-slate-400' }}">
                        {{ $msg->created_at->format('H:i') }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-center text-sm text-slate-400 mt-8">Belum ada pesan.</p>
        @endforelse
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('vendor.chats.messages.store', $conversation) }}"
          class="bg-white border border-slate-200 rounded-b-2xl px-3 py-3 flex items-end gap-2">
        @csrf
        <textarea name="body" rows="1" required placeholder="Tulis pesan..."
                  class="flex-1 resize-none border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('body') border-red-400 @enderror"
                  oninput="this.style.height='auto';this.style.height=(this.scrollHeight)+'px'"></textarea>
        <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white p-2.5 rounded-xl transition shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
        </button>
    </form>
</div>

@push('scripts')
<script>
    const el = document.getElementById('messages');
    if (el) { el.scrollTop = el.scrollHeight; }
</script>
@endpush
@endsection
