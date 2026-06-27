<?php

namespace Tests\Feature;
use App\Application\Notification\Commands\SendNotificationCommand;
use App\Application\Notification\UseCases\SendNotificationUseCase;
use App\Domain\Notification\ValueObjects\ChannelType;
use App\Domain\Notification\ValueObjects\NotificationStatus;
use App\Infrastructure\Notification\Jobs\SendNotificationJob;
use App\Infrastructure\Notification\NotificationServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(NotificationServiceProvider::class);
    }

    public function test_it_sends_email_notification_synchronously_and_saves_to_database(): void
    {
        Mail::fake();

        /** @var SendNotificationUseCase $useCase */
        $useCase = $this->app->make(SendNotificationUseCase::class);

        $command = new SendNotificationCommand(
            target: 'engineer@company.com',
            subject: 'System Alert',
            body: 'Server CPU is at 92%',
            channel: ChannelType::EMAIL,
            shouldPersist: true,
            async: false,
        );

        $resultMessage = $useCase->execute($command);

        $this->assertEquals(NotificationStatus::SENT, $resultMessage->getStatus());
        $this->assertNotNull($resultMessage->getSentAt());


        $this->assertDatabaseHas('notification_messages', [
            'recipient_target' => 'engineer@company.com',
            'subject' => 'System Alert',
            'channel' => 'email',
            'status' => 'sent',
        ]);
    }

    public function test_it_dispatches_async_job_to_queue_when_requested(): void
    {
        Queue::fake();

        /** @var SendNotificationUseCase $useCase */
        $useCase = $this->app->make(SendNotificationUseCase::class);

        $command = new SendNotificationCommand(
            target: '+1234567890',
            subject: 'OTP Code',
            body: 'Your code is 4829',
            channel: ChannelType::SMS,
            shouldPersist: true,
            async: true,
        );

        $resultMessage = $useCase->execute($command);

        $this->assertEquals(NotificationStatus::PENDING, $resultMessage->getStatus());

        $this->assertDatabaseHas('notification_messages', [
            'recipient_target' => '+1234567890',
            'channel' => 'sms',
            'status' => 'pending',
        ]);

        Queue::assertPushed(SendNotificationJob::class, function (SendNotificationJob $job) use ($resultMessage) {
            return $job->messageId === $resultMessage->getId();
        });
    }
}