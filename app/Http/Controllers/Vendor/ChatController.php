<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private function vendor()
    {
        return Auth::guard('vendor')->user();
    }

    /**
     * List percakapan vendor dengan semua penyewa.
     */
    public function index()
    {
        $conversations = $this->vendor()
            ->conversations()
            ->with(['user', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('vendor.chats.index', compact('conversations'));
    }

    /**
     * Tampilkan percakapan.
     */
    public function show(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $conversation->load('user');
        $messages = $conversation->messages()->get();

        $conversation->messages()
            ->where('sender_type', Message::SENDER_USER)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('vendor.chats.show', compact('conversation', 'messages'));
    }

    /**
     * Kirim pesan ke penyewa.
     */
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $conversation->messages()->create([
            'sender_type' => Message::SENDER_VENDOR,
            'sender_id'   => $this->vendor()->id,
            'body'        => $data['body'],
        ]);

        $conversation->touchLastMessage();

        return redirect()->route('vendor.chats.show', $conversation);
    }

    private function authorizeConversation(Conversation $conversation): void
    {
        abort_if($conversation->vendor_id !== $this->vendor()->id, 403);
    }
}
