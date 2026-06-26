<?php

namespace App\Infrastructure\Notification\Providers;

use App\Domain\Notification\Contracts\ChannelProviderInterface;
use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\ValueObjects\ChannelType;
use Illuminate\Support\Facades\Log;

class DatabaseChannelProvider implements ChannelProviderInterface
{
    public function supports(): ChannelType
    {
        return ChannelType::DATABASE;
    }

    public function send(NotificationMessage $message): void
    {
        // This provider is to fire real-time WebSocket events
        // (like Laravel Reverb / Pusher) to update the user's browser bell icon live

        Log::info("[Database Provider] In-App notification ready for User ID [{$message->getRecipient()->id}]. Triggering real-time broadcast UI update.");
    }
}