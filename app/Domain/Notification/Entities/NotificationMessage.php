<?php

namespace App\Domain\Notification\Entities;

use App\Domain\Notification\ValueObjects\ChannelType;
use App\Domain\Notification\ValueObjects\NotificationContent;
use App\Domain\Notification\ValueObjects\NotificationStatus;
use App\Domain\Notification\ValueObjects\Recipient;

use Carbon\Carbon;

class NotificationMessage
{
    private ?string $id;
    private Recipient $recipient;
    private NotificationContent $content;
    private ChannelType $channel;
    private NotificationStatus $status;
    private bool $shouldPersist;
    private Carbon $createdAt;
    private ?Carbon $sentAt;
    private ?string $errorMessage;

    public function __construct(
        Recipient $recipient,
        NotificationContent $content,
        ChannelType $channel,
        bool $shouldPersist = true,
        ?string $id = null,
    ) {
        $this->id = $id;
        $this->recipient = $recipient;
        $this->content = $content;
        $this->channel = $channel;
        $this->shouldPersist = $shouldPersist;
        $this->status = NotificationStatus::PENDING;
        $this->createdAt = new Carbon();
        $this->sentAt = null;
        $this->errorMessage = null;
    }

    public function markAsSent(): void
    {
        $this->sentAt = Carbon::now();
        $this->status = NotificationStatus::SENT;
        $this->errorMessage = null;
    }

    public function markAsFailed(string $error): void
    {
        $this->status = NotificationStatus::FAILED;
        $this->errorMessage = $error;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId(string $id): void
    {
        $this->id = $id;
    }
    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }
    public function getContent(): NotificationContent
    {
        return $this->content;
    }
    public function getChannel(): ChannelType
    {
        return $this->channel;
    }
    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }
    public function shouldPersist(): bool
    {
        return $this->shouldPersist;
    }
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }
    public function getSentAt(): ?Carbon
    {
        return $this->sentAt;
    }
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

}