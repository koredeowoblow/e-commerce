<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class UserChatController extends Controller
{
    // Get messages with a specific vendor
    public function index($vendorId, $messageID)
    {
        $userId = Auth::guard('api-user')->id();

        // Mark messages from the vendor as read
        Message::where('vendor_id', $vendorId)
            ->where('id', $messageID)
            ->where('user_id', $userId)
            ->where('sender_type', 'vendor')
            ->where('status', '!=', 'read')
            ->update([
                'status' => 'read',
                'read_at' => now()
            ]);

        $messages = Message::where('user_id', $userId)
            ->where('vendor_id', $vendorId)
            ->orderBy('created_at')
            ->get();

        return response()->json(['chat' => $messages]);
    }

    // Send message to a vendor
    public function send(Request $request, $vendorId)
    {
        $userId = Auth::guard('api-user')->id();

        $request->validate([
            'message' => 'required|string'
        ]);

        $isDelivered = true; // Add logic if needed to check vendor online status

        $message = Message::create([
            'user_id'     => $userId,
            'vendor_id'   => $vendorId,
            'sender_id'   => $userId,
            'sender_type' => 'user',
            'message'     => $request->message,
            'status'      => $isDelivered ? 'delivered' : 'sent',
        ]);
 
        return response()->json($message, 201);
    }

    // Unread count from vendor
    public function unreadFromVendor($vendorId)
    {
        $userId = Auth::user();

        $unread = Message::where('user_id', $userId)
            ->where('vendor_id', $vendorId)
            ->where('sender_type', 'vendor')
            ->where('status', '!=', 'read')
            ->count();

        return response()->json(['unread' => $unread]);
    }
}
