<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\RecoverPasswordDTO;
use App\Domain\User\Contracts\EventDispatcherInterface;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Events\PasswordRecoveryRequestedEvent;
use App\Domain\User\ValueObjects\Email;

class RecoverPassword
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(RecoverPasswordDTO $dto): bool
    {
        $email = new Email($dto->email);
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            // For Security Practice, I'm returning true even if email doesn't exist
            return true;
        }

        $token = rand(100000,999999);

        $this->eventDispatcher->dispatch(new PasswordRecoveryRequestedEvent($user, $token));

        return true;
    }
}