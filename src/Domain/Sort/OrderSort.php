<?php

namespace App\Domain\Sort;

enum OrderSort: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (OrderSort $orderSort) => $orderSort->value, OrderSort::cases());
    }
}
