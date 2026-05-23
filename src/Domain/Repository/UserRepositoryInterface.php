<?php

namespace App\Domain\Repository;

use App\Domain\Document\User;

interface UserRepositoryInterface
{
    /**
     * @param list<string> $phones
     */
    public function findUserByPhoneNumbers(array $phones): ?User;

    public function saveAndCommit(object $object): void;

    public function findUserById(string $id): ?User;

    public function commit(): void;

    /**
     * @return list<User>
     */
    public function findAndSortUsers(string $sortField, string $sortOrder): array;
}
