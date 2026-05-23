<?php

namespace App\Application\Command;

readonly class AddUserCommand
{
    /**
     * @param list<string> $phoneNumbers
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public array $phoneNumbers,
        public ?string $ip,
    ) {
    }
}
