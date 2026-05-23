<?php

namespace App\Infrastructure\ExceptionListener;

use App\Infrastructure\DTO\Error\ErrorResponseDTO;
use App\Infrastructure\ExceptionHandler\ExceptionMappingDTO;
use App\Infrastructure\ExceptionHandler\ExceptionMappingResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: 'kernel.exception', priority: -1)]
readonly class ApiExceptionListener
{
    private const string FALLBACK_TYPE = 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.1';

    public function __construct(
        private ExceptionMappingResolver $resolver,
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
        private bool $isDebug,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $mapping = $this->resolver->resolve(get_class($exception));
        if (null === $mapping) {
            $mapping = ExceptionMappingDTO::fromTypeAndCode(self::FALLBACK_TYPE, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable()) {
            $this->logger->error($exception, [
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        $type = $mapping->getType();
        $statusCode = $mapping->getCode();
        $title = Response::$statusTexts[$statusCode] ?? 'Unknown Error';
        $detail = $this->isDebug ? $exception->getMessage() : 'An unexpected error occurred. Please try again later';
        $trace = $this->isDebug ? $exception->getTraceAsString() : null;

        $dto = new ErrorResponseDTO($type, $statusCode, $title, $detail, null, $trace);
        $data = $this->serializer->serialize($dto, JsonEncoder::FORMAT,
            [
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            ]
        );

        $event->setResponse(new JsonResponse($data, $statusCode, ['Content-Type' => 'application/problem+json'], true));
    }
}
