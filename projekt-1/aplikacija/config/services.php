<?php

declare(strict_types=1);

use App\Command\ExampleCommand;
use App\Controller\HomeController;
use App\Controller\TournamentsController;
use App\Controller\PostController;
use App\Tools\Slugger;
use SimpleFW\DependencyInjection\Container;
use App\Command\ParseScheduleCommand;
use App\Parser\JsonScheduleParser;
use App\Parser\XmlScheduleParser;
use App\Command\ParseTeamCommand;
use App\Parser\JsonTeamParser;
use App\Parser\XmlTeamParser;
use App\Controller\EventController;
use App\Controller\TeamController;
use App\Controller\PlayerController;
use App\Controller\StandingsController;
use App\Command\CalculateStandingsCommand;

return static function (Container $container) {
    /*
     * Controllers
     */

    $container->addFactory(
        HomeController::class,
        static fn (Container $container) => new HomeController(
            $container->get(SimpleFW\ORM\EntityManager::class),
        ),
    );

    $container->addFactory(
        TournamentsController::class,
        static fn (Container $container) => new TournamentsController(
            $container->get(SimpleFW\ORM\EntityManager::class),
        ),
    );

    $container->addFactory(
        EventController::class,
        static fn (Container $container) => new EventController(
            $container->get(SimpleFW\ORM\EntityManager::class),
        ),
    );

    $container->addFactory(
        TeamController::class,
        static fn (Container $container) => new TeamController(
            $container->get(SimpleFW\ORM\EntityManager::class),
        ),
    );
    
    $container->addFactory(
        PlayerController::class,
        static fn (Container $container) => new PlayerController(
            $container->get(SimpleFW\ORM\EntityManager::class),
        ),
    );

    $container->addFactory(
        StandingsController::class,
        static fn (Container $container) => new StandingsController(
            $container->get(SimpleFW\ORM\EntityManager::class),
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

    $container->addFactory(
        ParseScheduleCommand::class,
        static fn (Container $container) => new ParseScheduleCommand(
            $container->get(SimpleFW\ORM\EntityManager::class),
            $container->get(JsonScheduleParser::class),
            $container->get(XmlScheduleParser::class),
            $container->getParameter('kernel.project_dir'),
        ),
    );

    $container->addFactory(
        ParseTeamCommand::class,
        static fn (Container $container) => new ParseTeamCommand(
            $container->get(SimpleFW\ORM\EntityManager::class),
            $container->get(JsonTeamParser::class),
            $container->get(XmlTeamParser::class),
            $container->getParameter('kernel.project_dir'),
        ),
    );

    $container->addFactory(
        CalculateStandingsCommand::class,
        static fn (Container $container) => new CalculateStandingsCommand(
            $container->get(SimpleFW\ORM\EntityManager::class),
        ),
    );

    /*
     * Other
     */

    $container->addFactory(Slugger::class, static fn () => new Slugger());

    $container->addFactory(JsonTeamParser::class, static fn (Container $container) => new JsonTeamParser(
        $container->get(Slugger::class),  $container->get(SimpleFW\ORM\EntityManager::class),
    ));

    $container->addFactory(JsonScheduleParser::class, static fn (Container $container) => new JsonScheduleParser(
        $container->get(Slugger::class),
    ));

    $container->addFactory(XmlTeamParser::class, static fn (Container $container) => new XmlTeamParser(
        $container->get(Slugger::class), $container->get(SimpleFW\ORM\EntityManager::class),
    ));

    $container->addFactory(XmlScheduleParser::class, static fn (Container $container) => new XmlScheduleParser(
        $container->get(Slugger::class),
    ));
};
