<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\TournamentsController;
use SimpleFW\Routing\Route;
use SimpleFW\Routing\Router;
use App\Controller\EventController;

return static function (Router $router) {
    $router
        ->addRoute(new Route('home', '/', HomeController::class))
        ->addRoute(new Route('tournaments', '/sport/{slug}', [TournamentsController::class, 'index']))
        ->addRoute(new Route('events', '/tournament/{slug}', [EventController::class, 'index']))
        ->addRoute(new Route('event', '/event/{id}', [EventController::class, 'details']))
        ->addRoute(new Route('sportJson', '/api', [HomeController::class, 'jsonSports']))
        ->addRoute(new Route('tournamentsJson', '/api/sport/{slug}', [TournamentsController::class, 'jsonTorunaments']))
        ->addRoute(new Route('eventsJson', '/api/tournament/{slug}', [EventController::class, 'jsonEvents']))
        ->addRoute(new Route('eventDetailsJson', '/api/event/{id}', [EventController::class, 'jsonDetails']))
    ;
};