<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Message;
use App\Http\Resources\MessageResource;

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

        $message = Message::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUserId,
            'message' => $request->message
        ]);

        broadcast(new PrivateMessageSent($message->message, $fromUser->id, $toUserId))->toOthers();

        Log::info('ChatController@send: PrivateMessageSent event dispatched successfully');

        return response()->json(['status' => 'Message sent', 'message' => $message]);
    } catch (Exception $e) {
        Log::error('ChatController@send: Error occurred', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'Failed to send message'], 500);
    }
}
public function getMessage(Request $request)
{
    $request->validate([
        'to_user_id' => 'required|exists:users,id',
    ]);

    $fromUser = $request->user();
    $toUserId = $request->to_user_id;

    $messages = Message::with(['sender', 'receiver'])
        ->where(function ($q) use ($fromUser, $toUserId) {
            $q->where('from_user_id', $fromUser->id)
              ->where('to_user_id', $toUserId);
        })
        ->orWhere(function ($q) use ($fromUser, $toUserId) {
            $q->where('from_user_id', $toUserId)
              ->where('to_user_id', $fromUser->id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

    if ($messages->isEmpty()) {
        return response()->json(['message' => 'No messages found'], 404);
    }

$messagesList = MessageResource::collection($messages);

return response()->json([
    'messages' => $messagesList
]);
}


public function getAllConversations(Request $request)
{
    $authId = $request->user()->id;

    $messages = Message::with(['sender', 'receiver'])
        ->where('from_user_id', $authId)
        ->orWhere('to_user_id', $authId)
        ->orderBy('created_at', 'desc')
        ->get();

    $conversations = [];

    foreach ($messages as $msg) {
        $userId = $msg->from_user_id === $authId ? $msg->to_user_id : $msg->from_user_id;

        if (!isset($conversations[$userId])) {
            $conversations[$userId] = $msg;
        }
    }

  $conversationsList = MessageResource::collection(array_values($conversations));

return response()->json([
    'conversations' => $conversationsList
]);



}
}