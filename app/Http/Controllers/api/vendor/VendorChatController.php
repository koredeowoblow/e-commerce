<?php

namespace App\Http\Controllers\api\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Events\MessageSent;


class VendorChatController extends Controller
{
    //  // Fetch conversation with a specific user
    public function index($userId)
    {
        $vendorId = Auth::guard('vendor')->id();

        // Mark messages from the user as "read"
        Message::where('user_id', $userId)
            ->where('vendor_id', $vendorId)
            ->where('sender_type', 'user')
            ->where('status', '!=', 'read')
            ->update([
                'status' => 'read',
                'read_at' => now()
            ]);

        // Fetch chat
        $messages = Message::where(function ($query) use ($vendorId, $userId) {
            $query->where('vendor_id', $vendorId)
                ->where('user_id', $userId);
        })->orderBy('created_at')->get();

        return response()->json([
            'chat' => $messages
        ]);
    }

    // Send message to user
    public function send(Request $request, $userId)
    {
        $vendorId = Auth::guard('vendor')->id();

        $request->validate([
            'message' => 'required|string'
        ]);

        // Assume message is delivered if user is active (you can expand this logic later)
        $isDelivered = true;

        $message = Message::create([
            'user_id'     => $userId,
            'vendor_id'   => $vendorId,
            'sender_id'   => $vendorId,
            'sender_type' => 'vendor',
            'message'     => $request->message,
            'status'      => $isDelivered ? 'delivered' : 'sent',
        ]);
        broadcast(new MessageSent($message, $vendorId, $userId))->toOthers();
        return response()->json($message, 201);
    }

    // Count unread messages from a specific user
    public function unreadFromUser($userId)
    {
        $vendorId = Auth::guard('vendor')->id();

        $unread = Message::where('vendor_id', $vendorId)
            ->where('user_id', $userId)
            ->where('sender_type', 'user')
            ->where('status', '!=', 'read')
            ->count();

        return response()->json(['unread' => $unread]);
    }
}
