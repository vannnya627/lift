<?php

namespace App\Tests\Infrastructure\Service;

use App\Infrastructure\Service\IpLocateCountryDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class IpLocateApiTest extends TestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetCountry()
    {
        $response = json_encode(['country' => 'Ukraine']);

        $mockResponse = new MockResponse($response,
            [
                'http_code' => 200,
                'response_headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

        $httpClient = new MockHttpClient($mockResponse);

        $service = new IpLocateCountryDefinition($httpClient);

        $result = $service->getCountry('8.8.8.8');

        $this->assertSame('Ukraine', $result);
        $this->assertSame('GET', $mockResponse->getRequestMethod());
        $this->assertSame('https://iplocate.io/api/lookup/8.8.8.8', $mockResponse->getRequestUrl());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetCountryWhenCountryIsNull()
    {
        $response = json_encode(['country' => null]);

        $mockResponse = new MockResponse($response,
            [
                'http_code' => 200,
                'response_headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
        $httpClient = new MockHttpClient($mockResponse);
        $service = new IpLocateCountryDefinition($httpClient);
        $result = $service->getCountry('127.0.0.1');

        $this->assertSame('Unknown Country', $result);
        $this->assertSame('GET', $mockResponse->getRequestMethod());
        $this->assertSame('https://iplocate.io/api/lookup/127.0.0.1', $mockResponse->getRequestUrl());
    }
}
