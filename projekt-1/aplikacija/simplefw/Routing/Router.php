<?php

declare(strict_types=1);

namespace SimpleFW\Routing;

use SimpleFW\HTTP\Request;
use SimpleFW\Routing\Exception\ResourceNotFoundException;

final class Router
{
    /** @var Route[] */
    private array $routes = [];
    private array $routePatterns = [];

    public function addRoute(Route $route): self
    {
        $this->routes[$route->name] = $route;

        preg_match_all('~{(\w+)}~', $route->path, $matches, \PREG_SET_ORDER);

        $placeholders = [];
        $paramPatterns = [];
        foreach ($matches as $match) {
            $placeholders[] = $match[0];
            $paramPatterns[$match[1]] = sprintf('(?P<%s>%s)', preg_quote($match[1]), $route->requirements[$match[1]] ?? '[\w\-]+');
        }

        $this->routePatterns[$route->name] = str_replace($placeholders, $paramPatterns, $route->path);

        arsort($this->routePatterns);

        return $this;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function match(Request $request): Route
    {
        foreach ($this->routePatterns as $routeName => $routePattern) {
            $route = $this->routes[$routeName];

            if (null !== $route->method && $route->method !== $request->method) {
                continue;
            }

            if (null !== $route->host && $route->host !== $request->host) {
                continue;
            }

            if (!preg_match(sprintf('~^%s$~i', $routePattern), $request->pathInfo, $matches)) {
                continue;
            }

            $request->attributes['_route_name'] = $route->name;
            $request->attributes['_route_params'] = $matches;

            return $route;
        }

        throw new ResourceNotFoundException($request->uri, $request->method);
    }
}
