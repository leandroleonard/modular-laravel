# Notification Module Architecture (DDD)

## Layer Breakdown

```text
app/
├── Domain/
│   └── Notification/
│       ├── Entities/
│       │   └── NotificationMessage.php
│       ├── ValueObjects/
│       │   ├── Recipient.php
│       │   ├── NotificationContent.php
│       │   ├── ChannelType.php
│       │   └── NotificationStatus.php
│       └── Contracts/
│           ├── NotificationRepositoryInterface.php
│           └── ChannelProviderInterface.php
│
├── Application/
│   └── Notification/
│       ├── Commands/
│       │   └── SendNotificationCommand.php
│       └── UseCases/
│           └── SendNotificationUseCase.php
│
└── Infrastructure/
    └── Notification/
        ├── Persistence/
        │   ├── Models/
        │   │   └── NotificationEloquentModel.php
        │   └── EloquentNotificationRepository.php
        ├── Providers/
        │   ├── EmailChannelProvider.php
        │   ├── SmsChannelProvider.php
        │   └── DatabaseChannelProvider.php
        └── Jobs/
            └── SendNotificationJob.php
```

## Core Design Principles
1. **Framework Independence in Domain**: Core entities and contracts do not import `Illuminate\*`.
2. **Pluggable Channels**: Any new channel (Slack, Push, Webhook) only requires implementing `ChannelProviderInterface`.
3. **Optional Persistence**: Notifications can be stored in the database for in-app UI display or audit logging.
4. **Async Support**: Application use case can dispatch to queue or run synchronously.
