<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        try {
            $request->validate([
                'to_user_id' => 'required|exists:users,id',
                'message' => 'required|string'
            ]);

            $fromUser = $request->user();
            $toUserId = $request->to_user_id;

            Log::info('ChatController@send: Incoming message request', [
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUserId,
                'message' => $request->message
            ]);

            broadcast(new PrivateMessageSent($request->message, $fromUser->id, $toUserId))->toOthers();

            Log::info('ChatController@send: PrivateMessageSent event dispatched successfully');

            return response()->json(['status' => 'Message sent']);
        } catch (Exception $e) {
            Log::error('ChatController@send: Error occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }
}
