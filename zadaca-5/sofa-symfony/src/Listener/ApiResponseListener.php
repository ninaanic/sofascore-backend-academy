<?php

declare(strict_types=1);

namespace App\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener]
class ApiResponseListener
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function __invoke(ViewEvent $event): void
    {
        $data = $event->getControllerResult();

        $data = $this->serializer->serialize($data, 'json', ['groups' => $event->getRequest()->attributes->get('groups', 'api')]);

        $event->setResponse(new JsonResponse($data, json: true));
    }

    public function onApiResponse($data) : JsonResponse 
    {
        $data = $this->serializer->serialize($data, 'json', ['groups' => 'apiResponse']);

        return new JsonResponse($data, json: true);
    }
}