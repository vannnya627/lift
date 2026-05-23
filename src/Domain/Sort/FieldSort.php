<?php

namespace App\Domain\Sort;

enum FieldSort: string
{
    case COUNTRY = 'country';
    case FIRSTNAME = 'firstName';
    case LASTNAME = 'lastName';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (FieldSort $fieldSort) => $fieldSort->value, FieldSort::cases());
    }
}
