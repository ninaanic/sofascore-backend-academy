<?php

declare(strict_types=1);

namespace SimpleFW\HTTP;

use SimpleFW\Console\CommandLoader;
use SimpleFW\Console\ListCommand;
use SimpleFW\DependencyInjection\Container;
use SimpleFW\HTTP\Exception\ControllerDoesNotReturnResponseException;
use SimpleFW\HTTP\Exception\HttpException;
use SimpleFW\HTTP\Exception\NotFoundHttpException;
use SimpleFW\Logger\FileLogger;
use SimpleFW\Logger\LoggerInterface;
use SimpleFW\ORM\ClassMetadataFactory;
use SimpleFW\ORM\Connection;
use SimpleFW\ORM\EntityManager;
use SimpleFW\ORM\QueryBuilder;
use SimpleFW\Routing\Exception\ResourceNotFoundException;
use SimpleFW\Routing\Router;
use SimpleFW\Templating\Templating;

abstract class Kernel
{
    private Container $container;

    public function boot(): void
    {
        $this->initializeContainer();
        $this->initializeRouter();
    }

    public function handle(Request $request): Response
    {
        $this->boot();

        try {
            try {
                $route = $this->container->get(Router::class)->match($request);
            } catch (ResourceNotFoundException $e) {
                throw new NotFoundHttpException($e->getMessage(), [], $e);
            }

            $controllerClass = $route->controller;
            if (\is_array($controllerClass)) {
                [$controllerClass, $action] = $controllerClass;
            } else {
                $action = '__invoke';
            }

            $controller = $this->container->has($controllerClass) ? $this->container->get($controllerClass) : new $controllerClass();

            $actionArguments = $this->resolveArguments($controller::class, $action, $request);
            $response = $controller->$action(...$actionArguments);

            if (!$response instanceof Response) {
                throw new ControllerDoesNotReturnResponseException($controllerClass, $action);
            }

            return $response;
        } catch (HttpException $e) {
            return new Response($e->getMessage(), $e->statusCode, $e->headers);
        } catch (\Throwable $e) {
            $logger = $this->container->get(LoggerInterface::class);
            $logger->log($e->getMessage(), ['exception' => $e]);

            throw $e;
        }
    }

    public function getContainer(): Container
    {
        if (!isset($this->container)) {
            throw new \LogicException('Cannot retrieve the container from a non-booted kernel.');
        }

        return $this->container;
    }

    abstract public function getProjectDir(): string;

    private function initializeContainer(): void
    {
        $container = new Container();

        $container->set('kernel', $this);

        $container->setParameter('kernel.project_dir', $this->getProjectDir());
        $container->setParameter('templating.base_path', $this->getProjectDir().'/templates');
        $container->setParameter('database.dsn', '');

        $parameters = $this->getProjectDir().'/config/parameters.php';
        if (file_exists($parameters)) {
            foreach (require $parameters as $paramName => $paramValue) {
                $container->setParameter($paramName, $paramValue instanceof \Closure ? $paramValue($container) : $paramValue);
            }
        }

        $container->addFactory(LoggerInterface::class, fn (Container $container) => new FileLogger(
            $this->getProjectDir().'/var/log/errors.log',
        ));
        $container->addFactory(Router::class, static fn () => new Router());
        $container->addFactory(Templating::class, static fn (Container $container) => new Templating(
            $container->getParameter('templating.base_path'),
        ));

        $container->addFactory(Connection::class, static fn (Container $container) => new Connection($container->getParameter('database.dsn')));
        $container->addFactory(QueryBuilder::class, static fn (Container $container) => new QueryBuilder(
            $container->get(Connection::class),
        ));
        $container->addFactory(ClassMetadataFactory::class, static fn () => new ClassMetadataFactory());
        $container->addFactory(EntityManager::class, static fn (Container $container) => new EntityManager(
            $container->get(QueryBuilder::class),
            $container->get(ClassMetadataFactory::class),
        ));

        $container->addFactory(ListCommand::class, static fn (Container $container) => new ListCommand(
            $container->get(CommandLoader::class),
        ));

        // Load services
        $services = $this->getProjectDir().'/config/services.php';
        if (file_exists($services)) {
            (require $services)($container);
        }

        // Register commands
        $commands = $this->getProjectDir().'/config/commands.php';
        $container->addFactory(CommandLoader::class, static fn (Container $container) => new CommandLoader(
            $container,
            ['list' => ListCommand::class] + (file_exists($commands) ? require $commands : []),
        ));

        $this->container = $container;
    }

    private function initializeRouter(): void
    {
        // Load routes
        $routes = $this->getProjectDir().'/config/routes.php';
        if (file_exists($routes)) {
            (require $routes)($this->container->get(Router::class));
        }
    }

    private function resolveArguments(string $controllerClass, string $action, Request $request): array
    {
        $reflection = new \ReflectionMethod($controllerClass, $action);

        $arguments = [];
        foreach ($reflection->getParameters() as $reflectionParameter) {
            if (Request::class === $type = $reflectionParameter->getType()->getName()) {
                $arguments[$reflectionParameter->name] = $request;
            } elseif (isset($request->attributes['_route_params'][$reflectionParameter->name])) {
                $argument = $request->attributes['_route_params'][$reflectionParameter->name];
                settype($argument, $type);
                $arguments[$reflectionParameter->name] = $argument;
            } elseif ($reflectionParameter->allowsNull() && !$reflectionParameter->isDefaultValueAvailable()) {
                $arguments[$reflectionParameter->name] = null;
            }
        }

        return $arguments;
    }
}
