<?php

namespace App\Tests\Application\Event;

use App\Application\Event\UserCreatedEvent;
use App\Application\Event\UserCreatedEventHandler;
use App\Domain\Document\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\CountryDefinitionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserCreatedEventHandlerTest extends TestCase
{
    private CountryDefinitionInterface|MockObject $countryDefinition;
    private UserRepositoryInterface|MockObject $userRepository;
    private UserCreatedEventHandler $userCreatedEventHandler;

    protected function setUp(): void
    {
        $this->countryDefinition = $this->createMock(CountryDefinitionInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userCreatedEventHandler = new UserCreatedEventHandler(
            $this->countryDefinition,
            $this->userRepository
        );
    }

    public function testInvoke()
    {
        $command = new UserCreatedEvent(
            userId: '1',
            ip: '127.0.0.1',
        );

        $user = new User();

        $this->userRepository->expects($this->once())
            ->method('findUserById')
            ->with(userId: $command->userId)
            ->willReturn($user);

        $this->countryDefinition->expects($this->once())
            ->method('getCountry')
            ->with($command->ip)
            ->willReturn('country');

        $this->userRepository->expects($this->once())
            ->method('commit');

        $this->userCreatedEventHandler->__invoke($command);

        $this->assertSame('country', $user->getCountry());
    }

    public function testInvokeWhenUserNotFound()
    {
        $command = new UserCreatedEvent(
            userId: '1',
            ip: '127.0.0.1',
        );

        $this->userRepository->expects($this->once())
            ->method('findUserById')
            ->with(userId: $command->userId)
            ->willReturn(null);

        $this->countryDefinition->expects($this->never())
            ->method('getCountry');

        $this->userRepository->expects($this->never())
            ->method('commit');

        $this->userCreatedEventHandler->__invoke($command);
    }

    public function testInvokeWhenIpIsNull()
    {
        $command = new UserCreatedEvent(
            userId: '1',
            ip: null
        );

        $user = new User();

        $this->userRepository->expects($this->once())
            ->method('findUserById')
            ->with(userId: $command->userId)
            ->willReturn($user);

        $this->countryDefinition->expects($this->never())
            ->method('getCountry');

        $this->userRepository->expects($this->once())
            ->method('commit');

        $this->userCreatedEventHandler->__invoke($command);

        $this->assertSame('Unknown Country', $user->getCountry());
    }
}
