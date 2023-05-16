<?php

declare(strict_types=1);

namespace App\Listener;

use App\Attribute\ApiController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[AsEventListener]
final class ApiControllerListener
{
    public function __construct(
        #[Autowire('%env(API_TOKEN)%')]
        private readonly string $apiToken,
    ) {
    }

    public function __invoke(ControllerEvent $event): void
    {
        if (!isset($event->getAttributes()[ApiController::class])) {
            return;
        }

        $event->getRequest()->attributes->set('is-api-controller', true);

        if ($this->apiToken !== $event->getRequest()->headers->get('X-Authorization')) {
            throw new AccessDeniedHttpException('Invalid token provided.');
        }
    }
}
