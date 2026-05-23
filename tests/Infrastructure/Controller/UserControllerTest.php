<?php

namespace App\Tests\Infrastructure\Controller;

use App\Domain\Document\User;
use App\Tests\AbstractWebTestCase;
use Doctrine\ODM\MongoDB\MongoDBException;
use Helmich\JsonAssert\JsonAssertions;

class UserControllerTest extends AbstractWebTestCase
{
    use JsonAssertions;

    public function testAddUser()
    {
        $payload = [
            'firstName' => 'David',
            'lastName' => 'Goggins',
            'phoneNumbers' => ['+380000000000', '+380111111111'],
        ];

        $this->client->jsonRequest(
            'POST',
            'api/v1/user',
            $payload
        );

        $this->assertResponseStatusCodeSame(202);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent();

        $schema = [
            'type' => 'object',
            'required' => ['message'],
            'properties' => [
                'message' => [
                    'type' => 'string',
                ],
            ],
        ];
        $this->assertJsonDocumentMatchesSchema($responseContent, $schema);
    }

    /**
     * @throws MongoDBException
     * @throws \Throwable
     */
    public function testAddUserThrowsExceptionUserAlreadyExists()
    {
        $user = new User()
            ->setFirstName('David')
            ->setLastName('Goggins')
            ->setPhoneNumbers(['+380000000000', '+380111111111']);

        $this->dm->persist($user);
        $this->dm->flush();

        $payload = [
            'firstName' => 'David',
            'lastName' => 'Goggins',
            'phoneNumbers' => ['+380000000000', '+380111111111'],
        ];

        $this->client->jsonRequest(
            'POST',
            'api/v1/user',
            $payload
        );

        $this->assertResponseStatusCodeSame(409);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json');

        $responseContent = $this->client->getResponse()->getContent();

        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['type', 'title', 'status', 'detail'],
            'properties' => [
                'type' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'status' => ['type' => 'integer'],
                'detail' => ['type' => 'string'],

                'errors' => ['type' => 'object'],

                'trace' => ['type' => 'string', 'null'],
            ],
        ];
        $this->assertJsonDocumentMatchesSchema($responseContent, $schema);
    }

    public function testAddUserThrowsExceptionValidationError()
    {
        $payload = [
            'firstName' => '',
            'lastName' => '',
            'phoneNumbers' => ['+38000dfs0000000', '+380111df111111'],
        ];

        $this->client->jsonRequest(
            'POST',
            'api/v1/user',
            $payload
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json');

        $responseContent = $this->client->getResponse()->getContent();
        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['type', 'title', 'status', 'detail'],
            'properties' => [
                'type' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'status' => ['type' => 'integer'],
                'detail' => ['type' => 'string'],

                'errors' => [
                    'type' => 'object',
                    'required' => ['firstName', 'lastName', 'phoneNumbers[0]', 'phoneNumbers[1]'],
                    'properties' => [
                        'firstName' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'lastName' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'phoneNumbers' => ['type' => 'array', 'items' => ['type' => 'string']],
                    ],
                ],
                'trace' => ['type' => 'string'],
            ],
        ];
        $this->assertJsonDocumentMatchesSchema($responseContent, $schema);
    }

    /**
     * @throws MongoDBException
     * @throws \Throwable
     */
    public function testGetUser()
    {
        $user1 = new User()
            ->setFirstName('A First Name')
            ->setLastName('A Last Name')
            ->setPhoneNumbers(['+380100000000', '+380011111111']);

        $user2 = new User()
            ->setFirstName('B First Name')
            ->setLastName('B Last Name')
            ->setPhoneNumbers(['+380001000000', '+380111119111']);

        $user3 = new User()
            ->setFirstName('C First Name')
            ->setLastName('C Last Name')
            ->setPhoneNumbers(['+380003000000', '+380111151111']);

        $this->dm->persist($user1);
        $this->dm->persist($user2);
        $this->dm->persist($user3);
        $this->dm->flush();

        $queryParams = http_build_query([
            'sort' => 'lastName',
            'order' => 'desc',
        ]);

        $this->client->jsonRequest(
            'GET',
            "api/v1/user?$queryParams",
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent();

        $schema = [
            'type' => 'object',
            'required' => ['data'],
            'properties' => [
                'data' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['firstName', 'lastName', 'country', 'ip', 'phoneNumbers'],
                        'properties' => [
                            'firstName' => ['type' => 'string'],
                            'lastName' => ['type' => 'string'],
                            'country' => ['type' => ['string', 'null']],
                            'ip' => ['type' => ['string', 'null']],
                            'phoneNumbers' => ['type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertJsonDocumentMatchesSchema($responseContent, $schema);
    }

    public function testGetUserThrowsExceptionInvalidParameter()
    {
        $queryParams = http_build_query([
            'sort' => 'bad',
            'order' => 'bad',
        ]);

        $this->client->jsonRequest(
            'GET',
            "api/v1/user?$queryParams",
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json');

        $responseContent = $this->client->getResponse()->getContent();

        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['type', 'title', 'status', 'detail'],
            'properties' => [
                'type' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'status' => ['type' => 'integer'],
                'detail' => ['type' => 'string'],

                'errors' => [
                    'type' => 'object',
                    'required' => ['sort', 'order'],
                    'properties' => [
                        'sort' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'order' => ['type' => 'array', 'items' => ['type' => 'string']],
                    ],
                ],
                'trace' => ['type' => 'string'],
            ],
        ];
        $this->assertJsonDocumentMatchesSchema($responseContent, $schema);
    }
}
