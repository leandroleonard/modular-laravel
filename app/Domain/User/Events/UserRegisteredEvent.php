<?php

namespace App\Domain\User\Events;
use App\Domain\User\Entities\User;
use Carbon\Carbon;

readonly class UserRegisteredEvent
{
    public Carbon $occuredAt;

    public function __construct(public User $user){
        $this->occuredAt = Carbon::now();
    }
}