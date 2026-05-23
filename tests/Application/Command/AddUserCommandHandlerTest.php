<?php

namespace App\Tests\Application\Command;

use App\Application\Command\AddUserCommand;
use App\Application\Command\AddUserCommandHandler;
use App\Application\Event\UserCreatedEvent;
use App\Domain\Document\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Tests\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class AddUserCommandHandlerTest extends AbstractTestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private MessageBusInterface|MockObject $messageBus;
    private AddUserCommandHandler $commandHandler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->commandHandler = new AddUserCommandHandler(
            $this->userRepository,
            $this->messageBus
        );
    }

    /**
     * @throws ExceptionInterface
     * @throws \ReflectionException
     */
    public function testInvoke()
    {
        $command = new AddUserCommand(
            'firstName',
            'lastName',
            ['+380000000000'],
            null,
        );

        $user = new User()
            ->setFirstName($command->firstName)
            ->setLastName($command->lastName)
            ->setPhoneNumbers($command->phoneNumbers)
            ->setIp($command->ip);

        $this->userRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($this->callback(function (User $savedUser) {
                $this->setEntityId($savedUser, '1');

                return true;
            }));

        $this->setEntityId($user, 1);

        $event = new UserCreatedEvent(
            userId: $user->getId(),
            ip: $user->getIp(),
        );

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope(new \stdClass()));

        $this->commandHandler->__invoke($command);
    }
}
