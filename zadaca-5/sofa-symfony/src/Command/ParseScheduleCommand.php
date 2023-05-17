<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Connection;
use App\Entity\Event;
use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Parser\JsonFeedParser;
use App\Parser\XmlFeedParser;
use App\Tools\Slugger;
use App\Tools\SportXmlDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Loader\LoaderChain;

#[AsCommand(
    name: 'app:parse:schedule',
    description: 'Parses the given file.',
)]
final class ParseScheduleCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Slugger $slugger,
        #[Autowire('%kernel.project_dir%/data')]
        private readonly string $dataDir,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('filename', InputArgument::REQUIRED, 'The name of the file to be parsed.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->hasArgument('filename')) {
            $output->writeln('Required argument filename missing.');

            return self::FAILURE;
        }

        $filename = $input->getArgument('filename');
        $filepath = $this->dataDir.'/'.$filename;

        if (!file_exists($filepath)) {
            $output->writeln(sprintf('No file with the name "%s" was found.', $filename));

            return self::FAILURE;
        }

        if (false === $content = @file_get_contents($filepath)) {
            $output->writeln(sprintf('Unable to read the file "%s".', $filename));

            return self::FAILURE;
        }

        $file_type = mime_content_type($filepath);

        if ($file_type === 'application/json') {

            $type = 'json';

        } else if ($file_type === 'text/xml') {

            $type = 'xml';
            
        } else {
            $output->writeln(sprintf('The file "%s" has an unknown media type "%s".', $filename, $file_type));
            return self::FAILURE;
        }


        $sport = $this->serializer->deserialize($content, \App\DTO\Sport::class, $type, [
            AbstractNormalizer::OBJECT_TO_POPULATE => new \App\DTO\Sport('', '', '', [])
        ]);

        $sport->slug = $this->slugger->slugify($sport->name);

        $sportEntity = $this->entityManager->getRepository(Sport::class)->findOneBy(['externalId' => $sport->id]);
        if (null === $sportEntity) {
            $sportEntity = new Sport($sport->name, $sport->slug, $sport->id, $sport->tournaments);
            $this->entityManager->persist($sportEntity);

        } else {
            $sportEntity->setName($sport->name);
            $sportEntity->setSlug($sport->slug);
            $sportEntity->setTournaments($sport->tournaments);
        }
        
        foreach ($sport->tournaments as $tournament) {
            $tournament->slug = $this->slugger->slugify($tournament->name);

            $tournamentEntity = $this->entityManager->getRepository(Tournament::class)->findOneBy(['externalId' => $tournament->id]);

            if (null === $tournamentEntity) {
                $tournamentEntity = new Tournament($tournament->name, $tournament->slug, $tournament->id, $tournament->events);
                $tournamentEntity->setSportId($sportEntity->getId());
                $this->entityManager->persist($tournamentEntity);
                
            } else {
                $tournamentEntity->setName($tournament->name);
                $tournamentEntity->setSlug($tournament->slug);
                $tournamentEntity->setEvents($tournament->events);
            }

            foreach ($tournament->events as $event) {

                $eventEntity = $this->entityManager->getRepository(Event::class)->findOneBy(['externalId' => $event->id]);
                $homeTeamEntity = $this->entityManager->getRepository(Team::class)->findOneBy(['externalId' => $event->home_team_id]);;
                $awayTeamEntity = $this->entityManager->getRepository(Team::class)->findOneBy(['externalId' => $event->away_team_id]);;

                if (null === $eventEntity) {
                    $eventEntity = new Event($event->id, $event->home_team_id, $event->away_team_id, $event->start_date, $event->home_score, $event->away_score);
                    $eventEntity->setTournamentId($tournamentEntity->getId());
                    $eventEntity->setHomeTeamId($homeTeamEntity->getId());
                    $eventEntity->setAwayTeamId($awayTeamEntity->getId());
                    $this->entityManager->persist($eventEntity);

                } else {
                    $eventEntity->setHomeTeamExteranlId($event->home_team_id);
                    $eventEntity->setAwayTeamExteranlId($event->away_team_id);
                    $eventEntity->setStartDate($event->start_date);
                    $eventEntity->setHomeScore($event->home_score);
                    $eventEntity->setAwayScore($event->away_score);
                }
            }
        }

        $output->writeln('File persisted successfully.');
        

        $this->entityManager->flush();

        return self::SUCCESS;
    }
}