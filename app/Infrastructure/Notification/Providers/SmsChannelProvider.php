<?php

namespace App\Infrastructure\Notification\Providers;

use App\Domain\Notification\Contracts\ChannelProviderInterface;
use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\ValueObjects\ChannelType;
use Illuminate\Support\Facades\Log;

class SmsChannelProvider implements ChannelProviderInterface
{
    public function supports(): ChannelType
    {
        return ChannelType::SMS;
    }

    public function send(NotificationMessage $message): void
    {
        $phone = $message->getRecipient()->target;
        $body = $message->getContent()->body;

        // Twilio/Vonage SDK client
        Log::info("[SMS Provider] Dispatched to {$phone}: \"{$body}\"");
    }
}