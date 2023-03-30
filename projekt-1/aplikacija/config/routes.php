<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\PostController;
use SimpleFW\Routing\Route;
use SimpleFW\Routing\Router;

return static function (Router $router) {
    $router
        ->addRoute(new Route('home', '/', HomeController::class))
        ->addRoute(new Route('post-index', '/post', [PostController::class, 'index'], method: 'GET'))
        ->addRoute(new Route('post-create', '/post', [PostController::class, 'create'], method: 'POST'))
        ->addRoute(new Route('post-show', '/post/{id}', [PostController::class, 'show'], method: 'GET'))
        ->addRoute(new Route('post-update', '/post/{id}', [PostController::class, 'update'], method: 'PATCH'))
        ->addRoute(new Route('post-delete', '/post/{id}', [PostController::class, 'delete'], method: 'DELETE'))
    ;
};
