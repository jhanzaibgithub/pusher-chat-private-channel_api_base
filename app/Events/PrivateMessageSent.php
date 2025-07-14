<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PrivateMessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $message;
    public $from;
    public $to;

    public function __construct($message, $from, $to)
    {
        $this->message = $message;
        $this->from = $from;
        $this->to = $to;

        Log::info('PrivateMessageSent event created', [
            'from_user_id' => $from,
            'to_user_id' => $to,
            'message' => $message
        ]);
    }

    public function broadcastOn()
    {
        $channel = 'chat.' . $this->to;

        Log::info('PrivateMessageSent@broadcastOn: Broadcasting on channel', [
            'channel' => $channel
        ]);

        return new PrivateChannel($channel);
    }

    public function broadcastWith()
    {
        Log::info('PrivateMessageSent@broadcastWith: Broadcasting data payload', [
            'message' => $this->message,
            'from' => $this->from
        ]);

        return [
            'message' => $this->message,
            'from' => $this->from,
        ];
    }
}
