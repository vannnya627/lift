<?php

namespace App\Infrastructure\Controller;

use App\Application\DTO\Request\SortRequestDTO;
use App\Application\DTO\Request\UserRequestDTO;
use App\Application\DTO\Response\UserResponseDTO;
use App\Application\Service\UserService;
use App\Infrastructure\DTO\Error\ErrorResponseDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag('UserController')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[OA\Post(
        operationId: 'api_add_user',
        description: 'Асинхронний запит на створення нового користувача',
        summary: 'Створення користувача',
    )]
    #[OA\Response(
        response: 202,
        description: 'Запит прийнято',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', default: 'request accepted'),
            ]
        )
    )]
    #[OA\Response(
        response: 409,
        description: 'Користувач вже існує',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class))
    )]
    #[OA\Response(
        response: 422,
        description: 'Помилка валідації',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class))
    )]
    #[OA\RequestBody(
        description: 'Тіло запиту',
        content: new OA\JsonContent(ref: new Model(type: UserRequestDTO::class))
    )]
    #[Route('api/v1/user', name: 'api_add_user', methods: ['POST'])]
    public function addUser(#[MapRequestPayload] UserRequestDTO $dto, Request $request): JsonResponse
    {
        $ip = $request->getClientIp();

        $this->userService->addUser(
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            phoneNumbers: $dto->phoneNumbers,
            ip: $ip
        );

        return $this->json(['message' => 'request accepted'], 202);
    }

    #[OA\Get(
        operationId: 'api_get_user',
        description: 'Запит на отримання користувачів',
        summary: 'Отримання користувачів',
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'date',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: UserResponseDTO::class))),
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Помилки валідації',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class))
    )]
    #[Route('api/v1/user', name: 'api_get_user', methods: ['GET'])]
    public function getUsers(#[MapQueryString] SortRequestDTO $request): JsonResponse
    {
        return $this->json(['data' => $this->userService->getUsers($request->sort, $request->order)]);
    }
}
