<?php

namespace App\Infrastructure\Service;

use App\Domain\Service\ClickHouseClientInterface;
use ClickHouseDB\Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ClickHouseClient implements ClickHouseClientInterface
{
    private ?Client $client = null;

    public function __construct(
        #[Autowire(env: 'CLICKHOUSE_HOST')]
        private readonly string $host,

        #[Autowire(env: 'CLICKHOUSE_PORT')]
        private readonly string $port,

        #[Autowire(env: 'CLICKHOUSE_USER')]
        private readonly string $user,

        #[Autowire(env: 'CLICKHOUSE_PASSWORD')]
        private readonly string $password,

        #[Autowire(env: 'CLICKHOUSE_DB')]
        private readonly string $db,
    ) {
    }

    private function getClient(): Client
    {
        if (null === $this->client) {
            $this->client = new Client([
                'host' => $this->host,
                'port' => $this->port,
                'username' => $this->user,
                'password' => $this->password,
            ]);

            $this->client->database($this->db);

            $this->client->setTimeout(5);
            $this->client->setConnectTimeOut(2);
        }

        return $this->client;
    }

    public function insertBatch(string $table, array $values): void
    {
        if (empty($values)) {
            return;
        }

        $this->getClient()->insertAssocBulk($table, $values);
    }

    //    public function select(string $sql, array $params = []): ?array
    //    {
    //        $response = $this->getClient()->select($sql, $params);
    //
    //        return $response->rows();
    //    }
}
