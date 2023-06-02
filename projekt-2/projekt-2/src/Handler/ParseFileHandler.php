<?php

namespace App\Handler;

use App\Entity\Sport;
use App\Message\ParseFile;
use App\Database\Connection;
use App\Entity\Event;
use App\Entity\Event as EntityEvent;
use App\Entity\Tournament;
use App\Parser\JsonParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;


class ParseFileHandler
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }
    public function __invoke(ParseFile $parseFile)
    {
        echo 'Processing '.$parseFile->filename, \PHP_EOL;

        $data = file_get_contents($parseFile->filename);

        if (strpos($parseFile->filename,"sport") !== false) {
            $this->parseSport($data);
        } else if (strpos($parseFile->filename,"Tournaments") !== false) {
            $this->parseTournaments($data);
        } else if (strpos($parseFile->filename,"Events") !== false) {
            $this->parseEvents($data);
        }
    }

    public function parseSport(string $data) {
        $sports = $this->serializer->deserialize($data, \App\DTO\Sport::class.'[]', 'json');
    
        foreach ($sports as $sport) {
            // get Entity
            $sportEntity = $this->entityManager->getRepository(Sport::class)->findOneBy(['external_id' => $sport->id]);

            if (null === $sportEntity) {
                // create Entity
                $sportEntity = new Sport($sport->name, $sport->slug, $sport->id);
                $this->entityManager->persist($sportEntity);
            } else {
                // update Entity
                $sportEntity->setName($sport->name);
                $sportEntity->setSlug($sport->slug);
            }
        }

        $this->entityManager->flush();

        echo 'Sports persisted successfully.', \PHP_EOL;
    }

    public function parseTournaments(string $data) {

        $tournamnets = $this->serializer->deserialize($data, \App\DTO\Tournament::class.'[]', 'json');

        //var_dump($tournamnets);
        
        foreach ($tournamnets as $tournament) {
            // get Entity
            $tournamentEntity = $this->entityManager->getRepository(Tournament::class)->findOneBy(['external_id' => $tournament->id]);

            if (null === $tournamentEntity) {
                // create Entity
                $tournamentEntity = new Tournament($tournament->name, $tournament->slug, $tournament->id, null, null);
                $tournamentEntity->setSportId($tournament->sport->id);
                $tournamentEntity->setCountryId($tournament->country->id);
                $this->entityManager->persist($tournamentEntity);
            } else {
                // update Entity
                $tournamentEntity->setName($tournament->name);
                $tournamentEntity->setSlug($tournament->slug);
            }
        }

        $this->entityManager->flush();
        

        echo 'Tournaments persisted successfully.', \PHP_EOL;
    }

    public function parseEvents(string $data) {

        $events = $this->serializer->deserialize($data, \App\DTO\Event::class.'[]', 'json');
        
        foreach ($events as $event) {
            // get Entity
            $eventEntity = $this->entityManager->getRepository(Event::class)->findOneBy(['external_id' => $event->id]);

            if (null === $eventEntity) {
                // create Entity
                $eventEntity = new Event($event->slug, $event->startDate, $event->status, null, $event->round, $event->id);
                $eventEntity->setTournamentId($event->tournament->id);
                $eventEntity->setHomeTeamId($event->homeTeam->id);
                $eventEntity->setAwayTeamId($event->awayTeam->id);
                $this->entityManager->persist($eventEntity);
            } else {
                // update Entity
                $eventEntity->setSlug($event->slug);
                $eventEntity->setStartDate($event->startDate);
                //$eventEntity->setHomeScore($event->home_score);
                //$eventEntity->setAwayScore($event->away_score);
            }
        }

        $this->entityManager->flush();
        

        echo 'Events persisted successfully.', \PHP_EOL;
    }
}