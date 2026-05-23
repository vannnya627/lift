<?php

namespace App\Application\DTO\Request;

use App\Domain\Sort\FieldSort;
use App\Domain\Sort\OrderSort;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class SortRequestDTO
{
    public function __construct(
        #[OA\Property(
            description: 'Поле за яким буде проходити сортування',
            default: 'lastName',
            enum: FieldSort::class,
        )]
        #[Assert\Choice(callback: [FieldSort::class, 'values'], message: 'Такого поля не існує. Оберіть один із: {{ choices }}')]
        public string $sort = 'lastName',
        #[OA\Property(
            description: 'Варіанти сортування',
            default: 'asc',
            enum: OrderSort::class,
        )]
        #[Assert\Choice(callback: [OrderSort::class, 'values'], message: 'Такого сортування не існує. Оберіть один із: {{ choices }}')]
        public string $order = 'asc',
    ) {
    }
}
