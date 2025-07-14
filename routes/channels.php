<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    $authorized = (int) $user->id === (int) $userId;

    Log::info('Broadcast::channel authorization called', [
        'auth_user_id' => $user->id,
        'channel_user_id' => $userId,
        'authorized' => $authorized
    ]);

    return $authorized;
});
