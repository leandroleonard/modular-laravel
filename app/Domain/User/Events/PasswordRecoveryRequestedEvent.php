<?php 

namespace App\Domain\User\Events;

use Carbon\Carbon;

readonly class PasswordRecoveryRequestedEvent
{
    public Carbon $createdAt;
    public function __construct(public User $user, public string $recoveryToken){
        $this->createdAt = Carbon::now();
    }
}