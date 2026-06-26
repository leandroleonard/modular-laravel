<?php

namespace App\Domain\User\Contracts;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObjects\Email;

interface UserRepositoryInterface
{
    public function save(User $user): User;
    public function findById(string $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function findAll(?array $filters = []): array;
}