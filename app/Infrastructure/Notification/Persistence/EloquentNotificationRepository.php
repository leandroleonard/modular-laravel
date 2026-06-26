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

    public function findAll(?string $userId = null, array $filters = []): array
    {
        $query = NotificationEloquentModel::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if (!empty($filters['status'])) {
            $statuses = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $query->whereIn('status', $statuses);
        }

        if (!empty($filters['channel'])) {
            $channels = is_array($filters['channel']) ? $filters['channel'] : [$filters['channel']];
            $query->whereIn('channel', $channels);
        }

        if (!empty($filters['recipient_target'])) {
            $query->where('recipient_target', 'LIKE', '%' . $filters['recipient_target'] . '%');
        }

        $dateFields = ['created_at', 'sent_at', 'read_at'];
        foreach ($dateFields as $field) {
            if (!empty($filters[$field])) {
                $dateFilter = $filters[$field];

                if (is_array($dateFilter)) {
                    foreach ($dateFilter as $operator => $value) {
                        $operator = strtoupper($operator);
                        if (in_array($operator, ['GTE', 'LTE', 'GT', 'LT', 'EQ'])) {
                            $query->where($field, $operator, $value);
                        }
                    }
                } else if (is_string($dateFilter)) {
                    $query->whereDate($field, $dateFilter);
                }
            }
        }

        if (!empty($filters['date_range'])) {
            $dateRange = $filters['date_range'];
            $field = $dateRange['field'] ?? 'created_at';

            if (isset($dateRange['from'])) {
                $query->where($field, '>=', $dateRange['from']);
            }

            if (isset($dateRange['to'])) {
                $query->where($field, '<=', $dateRange['to']);
            }
        }


        if (!empty($filters['order_by'])) {
            $direction = $filters['order_direction'] ?? 'asc';
            $query->orderBy($filters['order_by'], $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        } else {
            $query->limit(10);
        }

        if (!empty($filters['offset'])) {
            $query->offset($filters['offset']);
        }

        $models = $query->get();

        return $models->map(function (NotificationEloquentModel $model) {
            return $this->toDomain($model);
        })->toArray();
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