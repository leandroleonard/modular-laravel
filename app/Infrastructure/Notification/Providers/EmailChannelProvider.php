<?php

namespace App\Infrastructure\Notification\Providers;

use App\Domain\Notification\Contracts\ChannelProviderInterface;
use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\ValueObjects\ChannelType;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class EmailChannelProvider implements ChannelProviderInterface
{
    public function supports(): ChannelType
    {
        return ChannelType::EMAIL;
    }
    public function send(NotificationMessage $message): void
    {
        $recipientEmail = $message->getRecipient()->target;
        $subject = $message->getContent()->subject;
        $body = $message->getContent()->body;

        Mail::raw($body, function (Message $mail) use ($recipientEmail, $subject) {
            $mail->to($recipientEmail)
                ->subject($subject);
        });
    }
}