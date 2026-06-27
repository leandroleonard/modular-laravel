<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\RegisterDTO;
use App\Domain\User\Contracts\EventDispatcherInterface;
use App\Domain\User\Contracts\PasswordHasherInterface;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Events\UserRegisteredEvent;
use App\Domain\User\ValueObjects\Email;
use Carbon\Carbon;

class RegisterUser
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(RegisterDTO $dto): User
    {
        $email = new Email($dto->email);
        $hashedPassword = $this->passwordHasher->hash($dto->password);

        $user = new User(
            email: $email,
            password: $hashedPassword,
            name: $dto->name,
            roles: [$dto->role],
            createdAt: Carbon::now(),
        );

        $savedUser = $this->userRepository->save($user);

        $this->eventDispatcher->dispatch(new UserRegisteredEvent($savedUser));

        return $savedUser;
    }
}