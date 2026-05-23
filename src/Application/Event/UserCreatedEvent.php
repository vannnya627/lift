<?php

namespace App\Application\Event;

readonly class UserCreatedEvent
{
    public function __construct(
        public string $userId,
        public ?string $ip,
    ) {
    }
}
