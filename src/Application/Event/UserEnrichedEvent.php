<?php

namespace App\Application\Event;

readonly class UserEnrichedEvent
{
    public function __construct(
        public string $id,
    ) {
    }
}
