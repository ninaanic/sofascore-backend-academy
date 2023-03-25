<?php

declare(strict_types=1);

namespace SimpleFW\Routing;

use SimpleFW\HTTP\Request;

final class Router
{
    /** @var Route[] */
    private array $routes = [];

    public function addRoute(Route $route): self
    {
        $this->routes[$route->name] = $route;

        // @TODO: Zadatak 1

        // Sortirati regexe silazno prema duljini te početi od najduljeg
        $array = $this->routes;
        $callback = function (Route $a, Route $b) {
            return strlen($b->path) - strlen($a->path);
        };
        usort($array, $callback);

        return $this;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function match(Request $request): Route
    {
        // @TODO: Zadatak 1

        $matchedRoute = null;

        foreach($this->routes as $route) {
             // host, method
             if (!is_null($route->host) && ($route->host !== $request->host)) {
                continue;
            } if (!is_null($route->method) && ($route->method !== $request->method)) {
                continue;
            }

            // regex
            $pattern = "/\{(\w+?)\}/";
            $callback = function($matches) use ($route) {
                $paramName = $matches[1];
                $paramPattern = "[\w\-]+";
                if (!empty($route->requirements)) { 
                    $paramPattern = $route->requirements[$paramName];
                } 
                return "(?P<{$paramName}>{$paramPattern})";
            };
            $subject = $route->path;
            $regex = preg_replace_callback($pattern, $callback, $subject);

            // nađi rout-u koja odgovara 
            if (preg_match("#^{$regex}$#", $request->requestUri, $matches)) {
                $matchedRoute = $route;
                break;
            }
        }

        // nije nađena niti jedna rout-a
        if ($matchedRoute === null) {
            throw new Exception\ResourceNotFoundException($request->method, $request->uri);
        } 

        $request->attributes['_route_name'] = $matchedRoute->name;
        $request->attributes['_route_params'] = $matches;
        
        return $matchedRoute;
    }
}
