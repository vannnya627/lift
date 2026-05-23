<?php

namespace App\Infrastructure\Service;

use App\Domain\Service\CountryDefinitionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class IpLocateCountryDefinition implements CountryDefinitionInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getCountry(string $ip): string
    {
        $response = $this->httpClient->request(
            'GET',
            "https://iplocate.io/api/lookup/$ip"
        )->toArray();

        $country = $response['country'] ?? null;

        if (!is_string($country)) {
            return 'Unknown Country';
        }

        return $country;
    }
}
