<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.operations', function ($user) {
    return (bool) ($user->is_admin ?? false);
});

Broadcast::channel('user.{userId}', function ($user, int $userId) {
    return (int) $user->id === (int) $userId;
});
