<?php

namespace App\Application\Event;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\CountryDefinitionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
readonly class UserCreatedEventHandler
{
    public function __construct(
        private CountryDefinitionInterface $countryDefinition,
        private UserRepositoryInterface $userRepository,
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(UserCreatedEvent $userCreatedEvent): void
    {
        $user = $this->userRepository->findUserById($userCreatedEvent->userId);

        if (null === $user) {
            return;
        }

        if (null === $userCreatedEvent->ip) {
            $user->setCountry('Unknown Country');
        } else {
            $country = $this->countryDefinition->getCountry($userCreatedEvent->ip);
            $user->setCountry($country);
        }

        $this->userRepository->commit();

        $this->bus->dispatch(new UserEnrichedEvent($user->getId()));
    }
}
