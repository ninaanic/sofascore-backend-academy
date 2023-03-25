<?php

declare(strict_types=1);

use App\Command\ExampleCommand;
use App\Controller\HomeController;
use App\Controller\TournamentsController;
use App\Tools\Slugger;
use SimpleFW\DependencyInjection\Container;
use App\Database\Connection;
use App\Controller\EventController;
use App\Command\ParseCommand;

return static function (Container $container) {
    /*
     * Controllers
     */

    $container->addFactory(
        HomeController::class,
        static fn (Container $container) => new HomeController(
            $container->get(SimpleFW\Templating\Templating::class),
            $container->get(Connection::class),
        ),
    );

    $container->addFactory(
        TournamentsController::class,
        static fn (Container $container) => new TournamentsController(
            $container->get(SimpleFW\Templating\Templating::class), 
            $container->get(Connection::class),
        ),
    );

    $container->addFactory(
        EventController::class,
        static fn (Container $container) => new EventController(
            $container->get(SimpleFW\Templating\Templating::class), 
            $container->get(Connection::class),
        ),
    );
//    $container->addFactory(
//        'tournaments.controller',
//        static fn (Container $container) => new TournamentsController($container->get(SimpleFW\Templating\Templating::class)),
//    );

    /*
     * Commands
     */

    $container->addFactory(
        ExampleCommand::class,
        static fn (Container $container) => new ExampleCommand($container->get(Slugger::class)),
    );

    $container->addFactory(
        ParseCommand::class,
        static fn (Container $container) => new ParseCommand($container->get(Connection::class)),
    );

    /*
     * Other
     */

    $container->addFactory(Slugger::class, static fn () => new Slugger());

    // @TODO: Zadatak 3
    $container->addFactory(
        Connection::class,
        static fn (Container $container) => new Connection($container->getParameter('database.dsn'))
    );
};
