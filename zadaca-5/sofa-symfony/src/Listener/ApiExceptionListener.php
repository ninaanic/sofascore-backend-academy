<?php

declare(strict_types=1);

namespace App\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener]
class ApiExceptionListener 
{
    
    public function __construct()
    {
    }

    public function __invoke(ExceptionEvent $event): void {

        // ako controller ima ApiController atribut
        if ($event->getRequest()->attributes->get('ApiController')) {

            $exception = $event->getThrowable();

            if ($exception instanceof HttpExceptionInterface) {
                $statusCode = $exception->getStatusCode();
            } else {
                $statusCode = 500; // HTTP_INTERNAL_SERVICE_ERROR
            }

            $response = new JsonResponse(['error' => $exception->getMessage()]);
            $response->setStatusCode($statusCode);
            $response->headers->set('Content-Type', 'application/json');
            $event->setResponse($response);
        }
    }
}