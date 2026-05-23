<?php

namespace App\Application\Service;

use App\Application\Command\AddUserCommand;
use App\Application\DTO\Response\UserResponseDTO;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * @param list<string> $phoneNumbers
     *
     * @throws ExceptionInterface
     */
    public function addUser(string $firstName, string $lastName, array $phoneNumbers, ?string $ip): void
    {
        if ($this->userRepository->findUserByPhoneNumbers($phoneNumbers)) {
            throw new UserAlreadyExistsException();
        }

        $this->bus->dispatch(new AddUserCommand($firstName, $lastName, $phoneNumbers, $ip));
    }

    /**
     * @return list<UserResponseDTO>
     */
    public function getUsers(string $sortField, string $sortOrder): array
    {
        $users = $this->userRepository->findAndSortUsers($sortField, $sortOrder);

        return array_map(function ($user) {
            return new UserResponseDTO(
                firstName: $user->getFirstName(),
                lastName: $user->getLastName(),
                country: $user->getCountry(),
                ip: $user->getIp(),
                phoneNumbers: $user->getPhoneNumbers(),
            );
        }, $users);
    }
}
