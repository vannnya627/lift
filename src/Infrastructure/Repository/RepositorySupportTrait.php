<?php

namespace App\Infrastructure\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;

trait RepositorySupportTrait
{
    public function save(object $object): void
    {
        assert($this->getClassName() === get_class($object));
        $this->dm->persist($object);
    }

    /**
     * @throws MongoDBException
     * @throws \Throwable
     */
    public function commit(): void
    {
        $this->dm->flush();
    }

    public function remove(object $object): void
    {
        assert($this->getClassName() === get_class($object));
        $this->dm->remove($object);
    }

    /**
     * @throws \Throwable
     * @throws MongoDBException
     */
    public function saveAndCommit(object $object): void
    {
        $this->save($object);
        $this->commit();
    }

    /**
     * @throws MongoDBException
     * @throws \Throwable
     */
    public function removeAndCommit(object $object): void
    {
        $this->remove($object);
        $this->commit();
    }
}
