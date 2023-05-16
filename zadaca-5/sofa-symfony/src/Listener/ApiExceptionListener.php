<?php

declare(strict_types=1);

namespace App\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener]
final class ApiExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $isApiController = $event->getRequest()->attributes->get('is-api-controller', false);
        $exception = $event->getThrowable();

        if ($isApiController && $exception instanceof HttpExceptionInterface) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], $exception->getStatusCode()));
        }
    }
}
