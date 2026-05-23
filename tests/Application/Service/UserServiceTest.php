<?php

namespace App\Tests\Application\Service;

use App\Application\Command\AddUserCommand;
use App\Application\DTO\Response\UserResponseDTO;
use App\Application\Service\UserService;
use App\Domain\Document\User;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AllowMockObjectsWithoutExpectations]
class UserServiceTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private MessageBusInterface|MockObject $messageBus;
    private UserService $userService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->userService = new UserService($this->userRepository, $this->messageBus);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testAddUser()
    {
        $firstName = 'firstName';
        $lastName = 'lastName';
        $phoneNumber = ['+380000000000'];
        $ip = '127.0.0.1';

        $this->userRepository->expects($this->once())
            ->method('findUserByPhoneNumbers')
            ->with($phoneNumber)
            ->willReturn(null);

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with(new AddUserCommand($firstName, $lastName, $phoneNumber, $ip))
            ->willReturn(new Envelope(new \stdClass()));

        $this->userService->addUser($firstName, $lastName, $phoneNumber, $ip);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testAddUserThrowsExceptionWhenUserAlreadyExists()
    {
        $firstName = 'firstName';
        $lastName = 'lastName';
        $phoneNumber = ['+380000000000'];
        $ip = '127.0.0.1';

        $this->userRepository->expects($this->once())
            ->method('findUserByPhoneNumbers')
            ->with($phoneNumber)
            ->willReturn(new User());

        $this->expectException(UserAlreadyExistsException::class);

        $this->messageBus->expects($this->never())
            ->method('dispatch');

        $this->userService->addUser($firstName, $lastName, $phoneNumber, $ip);
    }

    public function testGetUsers(): void
    {
        $sortField = 'firstName';
        $sortOrder = 'asc';

        $user = new User()
            ->setFirstName('firstName')
            ->setLastName('lastName')
            ->setPhoneNumbers(['+380000000000'])
            ->setCountry('country')
            ->setIp('127.0.0.1');

        $this->userRepository->expects($this->once())
            ->method('findAndSortUsers')
            ->with($sortField, $sortOrder)
            ->willReturn([$user]);

        $expected = new UserResponseDTO(
            firstName: $user->getFirstName(),
            lastName: $user->getLastName(),
            country: $user->getCountry(),
            ip: $user->getIp(),
            phoneNumbers: $user->getPhoneNumbers(),
        );
        $result = $this->userService->getUsers($sortField, $sortOrder);

        $this->assertEquals([$expected], $result);
    }

    public function testGetUsersWhenUsersIsNotExists(): void
    {
        $sortField = 'firstName';
        $sortOrder = 'asc';

        $this->userRepository->expects($this->once())
            ->method('findAndSortUsers')
            ->with($sortField, $sortOrder)
            ->willReturn([]);

        $expected = [];

        $result = $this->userService->getUsers($sortField, $sortOrder);

        $this->assertEquals($expected, $result);
    }
}
