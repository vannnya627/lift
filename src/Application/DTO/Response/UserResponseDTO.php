<?php

namespace App\Application\DTO\Response;

readonly class UserResponseDTO
{
    /**
     * @param list<string> $phoneNumbers
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $country,
        public ?string $ip,
        public array $phoneNumbers,
    ) {
    }
}
