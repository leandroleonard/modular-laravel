<?php

namespace App\Infrastructure\Notification\Persistence;
use App\Domain\Notification\Entities\NotificationMessage;
use App\Domain\Notification\ValueObjects\ChannelType;
use App\Domain\Notification\ValueObjects\NotificationContent;
use App\Domain\Notification\ValueObjects\NotificationStatus;
use App\Infrastructure\Notification\Persistence\Models\NotificationEloquentModel;
use App\Domain\Notification\Contracts\NotificationRepositoryInterface;
use App\Domain\Notification\ValueObjects\Recipient;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function save(NotificationMessage $message): NotificationMessage
    {
        $id = $message->getId() ?? Str::uuid()->toString();
        $message->setId($id);

        NotificationEloquentModel::updateOrCreate(
            ['id' => $id],
            [
                'recipient_target' => $message->getRecipient()->target,
                'recipient_name' => $message->getRecipient()->name,
                'user_id' => $message->getRecipient()->id,
                'channel' => $message->getChannel()->value,
                'subject' => $message->getContent()->subject,
                'body' => $message->getContent()->body,
                'payload' => $message->getContent()->payload,
                'status' => $message->getStatus()->value,
                'error_message' => $message->getErrorMessage(),
                'read_at' => $message->getReadtAt()?->format('Y-m-d H:i:s'),
                'sent_at' => $message->getSentAt()?->format('Y-m-d H:i:s'),
                'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        );

        return $message;
    }

    public function findById(string $id): ?NotificationMessage
    {
        $model = NotificationEloquentModel::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toDomain($model);
    }

    private function toDomain(NotificationEloquentModel $model): NotificationMessage
    {
        $recipient = new Recipient(
            target: $model->recipient_target,
            name: $model->recipient_name,
            id: $model->user_id ?? null,
        );

        $content = new NotificationContent(
            subject: $model->subject,
            body: $model->body,
            payload: $model->payload ?? [],
        );

        $channel = ChannelType::from($model->channel);

        $readAt = $model->read_at
            ? Carbon::createFromMutable($model->read_at->toDateTime())
            : null;

        $message = new NotificationMessage(
            recipient: $recipient,
            content: $content,
            channel: $channel,
            shouldPersist: true,
            id: $model->id,
            readAt: $readAt,
        );

        if ($model->status === NotificationStatus::SENT->value) {
            $message->markAsSent();
        } elseif ($model->status === NotificationStatus::FAILED->value) {
            $message->markAsFailed($model->error_message ?? 'Unknown error');
        }

        return $message;
    }
}