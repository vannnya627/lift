<?php

namespace App\Infrastructure\DTO\Error;

use OpenApi\Attributes as OA;

readonly class ErrorResponseDTO
{
    /**
     * @param array<string, array<string>>|null $errors
     */
    public function __construct(
        public string $type,
        public int $status,
        public string $title,
        public string $detail,

        #[OA\Property(
            description: 'Помилки валідації, згруповані по полям',
            type: 'object',
            example: ['errorField' => ['Validation Error Message']],
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            )
        )]
        public ?array $errors = null,

        #[OA\Property(
            description: 'Трейс (Доступний тільки у debug=true)',
            example: '#0 someTrace....',
        )]
        public ?string $trace = null,
    ) {
    }
}
