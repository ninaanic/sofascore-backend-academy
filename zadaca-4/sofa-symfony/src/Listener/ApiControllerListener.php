<?php

declare(strict_types=1);

namespace App\Listener;

use App\Attribute\ApiController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsEventListener]
class ApiControllerListener 
{
    private string $apiToken;
    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }

    public function __invoke(ControllerEvent $event): void {
        $attributes = $event->getAttributes();
        if (empty($attributes)) return;

        $request = $event->getRequest();
        $header = $request->headers->get('X-Authorization');
        if ($header !== $this->apiToken) {
            throw new AccessDeniedException('Invalid API token');
        }

    }
}