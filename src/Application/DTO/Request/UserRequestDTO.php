<?php

namespace App\Application\DTO\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class UserRequestDTO
{
    /**
     * @param list<string> $phoneNumbers
     */
    public function __construct(
        #[OA\Property(
            description: "Ім'я користувача",
            example: 'David'
        )]
        #[Assert\NotBlank(message: 'Поле "firstName" не повинно бути відсутнім')]
        #[Assert\Length(max: 255, maxMessage: 'Поле "firstName" не повинно перевищувати 255 символів')]
        public string $firstName,
        #[OA\Property(
            description: 'Прізвище користувача',
            example: 'Goggins'
        )]
        #[Assert\NotBlank(message: 'Поле "lastName" не повинно бути відсутнім')]
        #[Assert\Length(max: 255, maxMessage: 'Поле "lastName" не повинно перевищувати 255 символів')]
        public string $lastName,

        #[OA\Property(
            description: 'Масив телефонних номерів користувача',
            type: 'array',
            items: new OA\Items(type: 'string'),
            example: [
                '+380971234567',
                '+380631234567',
            ]
        )]
        #[Assert\NotBlank(message: 'Поле "$phoneNumbers" не може бути порожнє')]
        #[Assert\Count(min: 1, minMessage: 'Повинен бути хоча б один номер телефону')]
        #[Assert\All([
            new Assert\Type('string', message: 'Номер телефону повинен бути рядком'),
            new Assert\Regex(
                pattern: '/^\+380\d{9}$/',
                message: 'Номер телефону повинен починатися на +380 та містити 12 цифр'
            ),
        ])]
        public array $phoneNumbers,
    ) {
    }
}
