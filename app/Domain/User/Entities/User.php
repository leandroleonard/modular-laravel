<?php

namespace App\Domain\User\Entities;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\HashedPassword;
use App\Domain\User\ValueObjects\Role;
use Carbon\Carbon;

class User
{
    /**
     * @param Role[] $roles
     */
    public function __construct(
        private Email $email,
        private HashedPassword $password,
        private string $name,
        private array $roles = [Role::USER],
        private ?string $id = null,
        private ?Carbon $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new Carbon();
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function hasPermission(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if (in_array($permission, $role->permissions(), true)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole(Role $role): void
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId(string $id): void
    {
        $this->id = $id;
    }
    public function getEmail(): Email
    {
        return $this->email;
    }
    public function getPassword(): HashedPassword
    {
        return $this->password;
    }
    public function getName(): string
    {
        return $this->name;
    }
    /** @return Role[] */
    public function getRoles(): array
    {
        return $this->roles;
    }
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }
}