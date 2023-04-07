<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\PostController;
use SimpleFW\Routing\Route;
use SimpleFW\Routing\Router;
use App\Controller\TournamentsController;
use App\Controller\EventController;
use App\Controller\TeamController;
use App\Controller\PlayerController;

return static function (Router $router) {
    $router
        ->addRoute(new Route('home', '/', HomeController::class))
        ->addRoute(new Route('sport-slug', '/sport/{slug}', [HomeController::class, 'slug'], method: 'GET'))

        ->addRoute(new Route('tournaments', '/sport/{slug}/tournaments', [TournamentsController::class, 'index'],  method: 'GET'))
        ->addRoute(new Route('tournament-slug', '/tournament/{slug}', [TournamentsController::class, 'slug'],  method: 'GET'))

        ->addRoute(new Route('event-tournament', '/tournament/{slug}/events', [EventController::class, 'index'],  method: 'GET'))
        ->addRoute(new Route('event-slug', '/event/{slug}', [EventController::class, 'slug'],  method: 'GET'))
        ->addRoute(new Route('event-update', '/event/{slug}', [EventController::class, 'update'], method: 'PATCH'))
        ->addRoute(new Route('event-delete', '/event/{slug}', [EventController::class, 'delete'], method: 'DELETE'))
        ->addRoute(new Route('event-team', '/team/{slug}/events', [EventController::class, 'event_team'],  method: 'GET'))
        ->addRoute(new Route('event-slugs', '/team/{slug}/tournament/{tournamentSlug}/events', [EventController::class, 'team_tournament_slug'],  method: 'GET'))
        ->addRoute(new Route('event-date-sport', '/sport/{slug}/events/{date}', [EventController::class, 'date_sport'],  method: 'GET'))
        ->addRoute(new Route('event-date-tournament', '/tournament/{slug}/events/{date}', [EventController::class, 'date_tournament'],  method: 'GET'))

        ->addRoute(new Route('team-slug', '/team/{slug}', [TeamController::class, 'slug'],  method: 'GET'))

        ->addRoute(new Route('players', '/team/{slug}/players', [PlayerController::class, 'index'],  method: 'GET'))
        ->addRoute(new Route('player-slug', '/player/{slug}', [PlayerController::class, 'slug'],  method: 'GET'))

        ->addRoute(new Route('post-index', '/post', [PostController::class, 'index'], method: 'GET'))
        ->addRoute(new Route('post-create', '/post', [PostController::class, 'create'], method: 'POST'))
        ->addRoute(new Route('post-show', '/post/{id}', [PostController::class, 'show'], method: 'GET'))
        ->addRoute(new Route('post-update', '/post/{id}', [PostController::class, 'update'], method: 'PATCH'))
        ->addRoute(new Route('post-delete', '/post/{id}', [PostController::class, 'delete'], method: 'DELETE'))
    ;
};
