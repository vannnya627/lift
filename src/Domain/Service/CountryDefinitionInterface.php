<?php

namespace App\Domain\Service;

interface CountryDefinitionInterface
{
    public function getCountry(string $ip): string;
}
