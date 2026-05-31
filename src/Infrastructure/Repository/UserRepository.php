<?php

namespace App\Infrastructure\Repository;

use App\Domain\Document\User;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @extends DocumentRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array<string, mixed> $criteria, array<string, mixed>|null $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends DocumentRepository implements UserRepositoryInterface
{
    use RepositorySupportTrait;

    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(User::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @param list<string> $phones
     */
    public function findUserByPhoneNumbers(array $phones): ?User
    {
        return $this->findOneBy(['phoneNumbers' => $phones]);
    }

    /**
     * @throws MappingException
     * @throws LockException
     */
    public function findUserById(string $id): ?User
    {
        return $this->find($id);
    }

    /**
     * @return list<User>
     */
    public function findAndSortUsers(string $sortField, string $sortOrder): array
    {
        return array_values($this->findBy(criteria: [], orderBy: [$sortField => $sortOrder]));
    }
}
