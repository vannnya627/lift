<?php

namespace App\Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected DocumentManager $dm;

    protected KernelBrowser $client;

    /**
     * @throws MongoDBException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->dm = self::getContainer()->get('doctrine_mongodb.odm.document_manager');

        $this->purgeDatabase();
    }

    /**
     * @throws MongoDBException
     */
    private function purgeDatabase(): void
    {
        $metadatas = $this->dm->getMetadataFactory()->getAllMetadata();

        foreach ($metadatas as $metadata) {
            $this->dm->getDocumentCollection($metadata->getName())->deleteMany([]);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (isset($this->dm)) {
            $this->dm->close();
        }
    }
}
