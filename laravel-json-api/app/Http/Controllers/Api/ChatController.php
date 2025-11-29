<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    const MAX_ACTIVE_CHATS = 3;

    // User: Buat room baru atau ambil existing room
    public function createOrGetRoom(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string',
            'visitor_email' => 'nullable|email',
            'visitor_phone' => 'nullable|string'
        ]);

        // Hitung jumlah chat yang sedang aktif
        $activeCount = ChatRoom::where('status', 'active')->count();
        
        // Tentukan status: active atau queued
        $status = $activeCount < self::MAX_ACTIVE_CHATS ? 'active' : 'queued';
        
        // Jika queued, hitung posisi antrian
        $queuePosition = null;
        if ($status === 'queued') {
            $queuePosition = ChatRoom::where('status', 'queued')->count() + 1;
        }

        $room = ChatRoom::create([
            'visitor_name' => $request->visitor_name,
            'visitor_email' => $request->visitor_email,
            'visitor_phone' => $request->visitor_phone,
            'status' => $status,
            'queue_position' => $queuePosition,
            'last_message_at' => now()
        ]);

        return response()->json([
            'success' => true, 
            'data' => $room,
            'message' => $status === 'queued' 
                ? "Anda berada di antrian posisi ke-{$queuePosition}. Admin akan segera membalas Anda." 
                : "Chat dimulai. Admin akan segera membalas Anda."
        ]);
    }

    // User: Kirim pesan
    public function sendMessage(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:chat_rooms,id',
            'message' => 'required|string',
            'sender_type' => 'required|in:user,admin'
        ]);

        $message = ChatMessage::create([
            'chat_room_id' => $request->room_id,
            'sender_type' => $request->sender_type,
            'message' => $request->message
        ]);

        // Update last message time
        ChatRoom::find($request->room_id)->update([
            'last_message_at' => now(),
            'is_read' => $request->sender_type === 'user' ? false : true
        ]);

        return response()->json(['success' => true, 'data' => $message], 201);
    }

    // User & Admin: Ambil pesan di room
    public function getMessages($roomId)
    {
        $messages = ChatMessage::where('chat_room_id', $roomId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $messages]);
    }

    // Admin: List semua chat rooms
    public function getRooms()
    {
        $rooms = ChatRoom::with('latestMessage')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $rooms]);
    }

    // Admin: Mark room as read
    public function markAsRead($roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $room->update(['is_read' => true]);

        // Mark all messages as read
        ChatMessage::where('chat_room_id', $roomId)
            ->where('sender_type', 'user')
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // Admin: Delete room
    public function deleteRoom($roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $wasActive = $room->status === 'active';
        
        $room->delete();
        
        // Jika yang dihapus adalah chat aktif, aktifkan chat dari queue
        if ($wasActive) {
            $this->activateNextInQueue();
        }
        
        return response()->json(['success' => true]);
    }

    // Get unread count for admin badge
    public function getUnreadCount()
    {
        $count = ChatRoom::where('is_read', false)->count();
        return response()->json(['success' => true, 'count' => $count]);
    }

    // Admin: Close chat (tutup chat tapi tidak hapus)
    public function closeChat($roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $room->update(['status' => 'closed']);
        
        // Aktifkan chat berikutnya dari queue
        $this->activateNextInQueue();
        
        return response()->json(['success' => true, 'message' => 'Chat ditutup']);
    }

    // Helper: Aktifkan chat berikutnya dari antrian
    private function activateNextInQueue()
    {
        $nextInQueue = ChatRoom::where('status', 'queued')
            ->orderBy('queue_position', 'asc')
            ->first();

        if ($nextInQueue) {
            $nextInQueue->update([
                'status' => 'active',
                'queue_position' => null
            ]);

            // Update posisi antrian lainnya
            ChatRoom::where('status', 'queued')
                ->where('queue_position', '>', $nextInQueue->queue_position)
                ->decrement('queue_position');
        }
    }

    // Get queue status for user
    public function getQueueStatus($roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        
        return response()->json([
            'success' => true,
            'data' => [
                'status' => $room->status,
                'queue_position' => $room->queue_position,
                'active_chats' => ChatRoom::where('status', 'active')->count(),
                'queued_chats' => ChatRoom::where('status', 'queued')->count()
            ]
        ]);
    }
}
