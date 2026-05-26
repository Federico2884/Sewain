<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * List percakapan user dengan semua vendor.
     */
    public function index()
    {
        $conversations = Auth::user()
            ->conversations()
            ->with(['vendor', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('user.chats.index', compact('conversations'));
    }

    /**
     * Mulai percakapan baru dengan vendor (atau buka yang sudah ada).
     */
    public function start(Vendor $vendor)
    {
        $conversation = Conversation::between(Auth::id(), $vendor->id);

        return redirect()->route('user.chats.show', $conversation);
    }

    /**
     * Tampilkan percakapan.
     */
    public function show(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $conversation->load('vendor');
        $messages = $conversation->messages()->get();

        // Tandai semua pesan dari vendor sebagai dibaca
        $conversation->messages()
            ->where('sender_type', Message::SENDER_VENDOR)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('user.chats.show', compact('conversation', 'messages'));
    }

    /**
     * Kirim pesan ke vendor.
     */
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $conversation->messages()->create([
            'sender_type' => Message::SENDER_USER,
            'sender_id'   => Auth::id(),
            'body'        => $data['body'],
        ]);

        $conversation->touchLastMessage();

        return redirect()->route('user.chats.show', $conversation);
    }

    private function authorizeConversation(Conversation $conversation): void
    {
        abort_if($conversation->user_id !== Auth::id(), 403);
    }
}
