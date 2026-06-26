<?php

namespace App\Infrastructure\Notification\Jobs;

use App\Application\Notification\UseCases\SendNotificationUseCase;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\ValueObjects\NotificationStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(public readonly string $messageId)
    {
    }

    public function handle(
        NotificationRepositoryInterface $repository,
        SendNotificationUseCase $useCase,
    ): void {
        $message = $repository->findById($this->messageId);

        if ($message === null || $message->getStatus() !== NotificationStatus::PENDING) {
            return;
        }

        $useCase->send($message);
    }
}