<?php 

namespace App\Domain\User\Events;
use App\Domain\User\Entities\User;

use Carbon\Carbon;

readonly class PasswordRecoveryRequestedEvent
{
    public Carbon $createdAt;
    public function __construct(public User $user, public string $recoveryToken){
        $this->createdAt = Carbon::now();
    }
}