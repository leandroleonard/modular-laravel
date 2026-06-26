<?php

namespace App\Application\Notification\UseCases;

use App\Application\Notification\Commands\SendNotificationCommand;
use App\Domain\Notification\Contracts\ChannelResolverInterface;
use App\Domain\Notification\Contracts\NotificationQueueDispatcherInterface;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\ValueObjects\NotificationContent;
use App\Domain\Notification\ValueObjects\Recipient;
use Throwable;

class SendNotificationUseCase
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository,
        private readonly ChannelResolverInterface $channelResolver,
        private readonly ?NotificationQueueDispatcherInterface $queueDispatcher = null,
    ) {}

    /**
     * Entry point for dispatching a notification from controllers, commands, or events.
     */
    public function execute(SendNotificationCommand $command): NotificationMessage
    {
        $recipient = new Recipient($command->target, $command->recipientName, $command->userId);
        $content = new NotificationContent($command->subject, $command->body, $command->payload);
        
        $message = new NotificationMessage(
            recipient: $recipient,
            content: $content,
            channel: $command->channel,
            shouldPersist: $command->shouldPersist,
        );

        if ($message->shouldPersist()) {
            $message = $this->repository->save($message);
        }

        if ($command->async && $this->queueDispatcher !== null) {
            $this->queueDispatcher->dispatch($message);
            return $message;
        }

        $this->send($message);

        return $message;
    }

    /**
     * Execute the physical channel delivery. 
     * Can be called directly synchronously or invoked later by a Queue Job worker.
     * 
     * @throws Throwable
     */
    public function send(NotificationMessage $message): void
    {
        try {
            $provider = $this->channelResolver->resolve($message->getChannel());
            $provider->send($message);
            
            $message->markAsSent();
        } catch (Throwable $e) {
            $message->markAsFailed($e->getMessage());
            
            if ($message->shouldPersist()) {
                $this->repository->save($message);
            }
            
            throw $e;
        }

        if ($message->shouldPersist()) {
            $this->repository->save($message);
        }
    }
}
