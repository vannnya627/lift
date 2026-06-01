<?php

namespace App\Domain\Service;

interface ClickHouseClientInterface
{
    /**
     * @param array<int, array{_id: string, firstName: string, lastName: string, country: string|null, ip: string|null, phoneNumbers:list<string>, created_at: \DateTimeImmutable}> $values
     */
    public function insertBatch(string $table, array $values): void;

    //    public function select(string $sql, array $params = []): ?array;
}
