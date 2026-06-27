<?php

namespace App\Application\User\UseCases;
use App\Domain\User\Services\UserAuthService;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\User\Entities\User;
use App\Application\User\DTOs\LoginDTO;
use App\Domain\User\ValueObjects\Email;

class AuthenticateUser
{
    public function __construct(
        private readonly UserAuthService $userAuthService,
    ) {
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function execute(LoginDTO $dto): User
    {
        $email = new Email($dto->email);

        return $this->userAuthService->authenticate($email, $dto->password);
    }
}