<?php

namespace App\Application\Event;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\ClickHouseClientInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;

#[AsMessageHandler]
class ClickHouseBatchEventHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly ClickHouseClientInterface $client,
    ) {
    }

    public function __invoke(UserEnrichedEvent $message, ?Acknowledger $ack = null): mixed
    {
        return $this->handle($message, $ack);
    }

    protected function process(array $jobs): void
    {
        try {
            /** @var list<string> $ids */
            $ids = [];
            /** @var UserEnrichedEvent $message */
            foreach ($jobs as [$message, $ack]) {
                $ids[] = $message->id;
            }

            if (empty($ids)) {
                foreach ($jobs as [$message, $ack]) {
                    $ack->ack();
                }

                return;
            }

            $users = $this->userRepository->findUsersByIds($ids);

            $data = [];
            foreach ($users as $user) {
                $data[] = [
                    '_id' => $user->getId(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'country' => $user->getCountry(),
                    'ip' => $user->getIp(),
                    'phoneNumbers' => $user->getPhoneNumbers(),
                    'created_at' => $user->getCreatedAt(),
                ];
            }

            $this->client->insertBatch('user_profiles', $data);

            foreach ($jobs as [$message, $ack]) {
                $ack->ack();
            }
        } catch (\Throwable $e) {
            foreach ($jobs as [$message, $ack]) {
                $ack->nack($e);
            }
        }
    }

    protected function getBatchSize(): int
    {
        return 3;
    }
}
