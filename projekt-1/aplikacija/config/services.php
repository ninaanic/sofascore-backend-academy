<?php

declare(strict_types=1);

use App\Command\ExampleCommand;
use App\Controller\HomeController;
use App\Controller\PostController;
use App\Tools\Slugger;
use SimpleFW\DependencyInjection\Container;
use App\Database\Connection;

return static function (Container $container) {
    /*
     * Controllers
     */

    $container->addFactory(
        HomeController::class,
        static fn (Container $container) => new HomeController(
            $container->get(SimpleFW\Templating\Templating::class),
        ),
    );

    $container->addFactory(
        PostController::class,
        static fn (Container $container) => new PostController(
            $container->get(SimpleFW\ORM\EntityManager::class),
        ),
    );

    /*
     * Commands
     */

    $container->addFactory(
        ExampleCommand::class,
        static fn (Container $container) => new ExampleCommand($container->get(Slugger::class)),
    );

    /*
     * Other
     */

    $container->addFactory(Slugger::class, static fn () => new Slugger());

    $container->addFactory(
        Connection::class,
        static fn (Container $container) => new Connection($container->getParameter('database.dsn'))
    );
};
