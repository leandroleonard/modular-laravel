<?php
namespace App\Domain\User\Services;

use App\Domain\User\Contracts\PasswordHasherInterface;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Exceptions\InvalidCredentialsException;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\Entities\User;
class UserAuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * Authenticate a user by email and plain text password.
     * @throws InvalidCredentialsException
     */
    public function authenticate(Email $email, string $plainPassword): User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user === null || !$this->passwordHasher->verify($plainPassword, $user->getPassword())) {
            throw new InvalidCredentialsException();
        }

        return $user;
    }
}