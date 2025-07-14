<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            // 'from_user_id' => $this->from_user_id,
            // 'to_user_id' => $this->to_user_id,
            'sender' => [
                'id' => $this->sender?->id,
                'name' => $this->sender?->name,
                'email' => $this->sender?->email,
            ],
            'receiver' => [
                'id' => $this->receiver?->id,
                'name' => $this->receiver?->name,
                'email' => $this->receiver?->email,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
