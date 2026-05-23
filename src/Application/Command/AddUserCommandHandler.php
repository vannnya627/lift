<?php

namespace App\Application\Command;

use App\Application\Event\UserCreatedEvent;
use App\Domain\Document\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
readonly class AddUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(AddUserCommand $command): void
    {
        $user = new User()
            ->setFirstName($command->firstName)
            ->setLastName($command->lastName)
            ->setPhoneNumbers($command->phoneNumbers)
            ->setIp($command->ip);

        $this->userRepository->saveAndCommit($user);

        $this->bus->dispatch(new UserCreatedEvent(
            userId: $user->getId(),
            ip: $user->getIp()
        ));
    }
}
