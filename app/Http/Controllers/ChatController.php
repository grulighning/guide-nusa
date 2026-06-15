<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Tampilkan daftar percakapan untuk user yang login.
     */
    public function index()
    {
        $userId = auth()->id();

        // Ambil semua pesan yang melibatkan user ini, urutkan dari terbaru
        $messages = ChatMessage::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver', 'booking'])
            ->latest()
            ->get();

        // Kelompokkan berdasarkan lawan bicara: ambil pesan terakhir per lawan bicara
        $conversations = [];
        $seen = [];

        foreach ($messages as $msg) {
            $otherId = $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;

            if (! isset($seen[$otherId])) {
                $seen[$otherId] = true;
                $otherUser = $msg->sender_id === $userId ? $msg->receiver : $msg->sender;

                // Hitung pesan belum dibaca dari lawan bicara
                $unreadCount = ChatMessage::where('sender_id', $otherId)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();

                $conversations[] = [
                    'user'         => $otherUser,
                    'last_message' => $msg,
                    'unread_count' => $unreadCount,
                ];
            }
        }

        // Urutkan: yang ada pesan baru (belum dibaca) di atas, lalu berdasarkan waktu terbaru
        usort($conversations, function ($a, $b) {
            if ($a['unread_count'] > 0 && $b['unread_count'] === 0) return -1;
            if ($a['unread_count'] === 0 && $b['unread_count'] > 0) return 1;
            return $b['last_message']->created_at->timestamp - $a['last_message']->created_at->timestamp;
        });

        return view('chat.index', compact('conversations'));
    }

    /**
     * Tampilkan percakapan dengan user tertentu.
     */
    public function show(User $user)
    {
        $userId = auth()->id();

        if ($user->id === $userId) {
            return redirect()->route('chat.index')->with('error', 'Tidak bisa chat dengan diri sendiri.');
        }

        // Ambil semua pesan antara kedua user
        $messages = ChatMessage::where(function ($q) use ($userId, $user) {
            $q->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($userId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $userId);
        })
            ->with(['sender', 'receiver'])
            ->oldest()
            ->get();

        // Tandai pesan dari lawan bicara sebagai sudah dibaca
        ChatMessage::where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chat.show', compact('messages', 'user'));
    }

    /**
     * Kirim pesan baru.
     */
    public function store(Request $request, User $user)
    {
        $userId = auth()->id();

        if ($user->id === $userId) {
            return back()->with('error', 'Tidak bisa mengirim pesan ke diri sendiri.');
        }

        $validated = $request->validate([
            'message'    => 'required|string|max:5000',
            'booking_id' => 'nullable|exists:pemandu_bookings,id',
        ]);

        ChatMessage::create([
            'sender_id'   => $userId,
            'receiver_id' => $user->id,
            'message'     => $validated['message'],
            'booking_id'  => $validated['booking_id'] ?? null,
            'is_read'     => false,
        ]);

        return redirect()->route('chat.show', $user)
            ->with('success', 'Pesan berhasil dikirim.');
    }

    /**
     * Ambil jumlah pesan belum dibaca (untuk badge navbar).
     */
    public static function unreadCount(): int
    {
        if (! auth()->check()) return 0;

        return ChatMessage::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }
}
