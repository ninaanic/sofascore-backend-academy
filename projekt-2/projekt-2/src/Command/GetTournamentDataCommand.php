<?php

declare(strict_types=1);

namespace App\Command;
use App\Database\Connection;
use App\Message\ParseFile;
use App\Entity\Sport;
use App\Message\ProcessDataMessage;
use Doctrine\DBAL\ArrayParameters\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:get:tournament:data',
    description: 'Get tournament data from Provider.',
)]
final class GetTournamentDataCommand extends Command
{
    
    public function __construct(
        private readonly MessageBus $messageBus,
        private readonly HttpClientInterface $client,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int {

        $dir = 'data/Tournaments';

        // get sports
        $sportEntity = $this->entityManager->getRepository(Sport::class)->findAll();
        
        foreach ($sportEntity as $sport) {
            $slug = $sport->getSlug();

            $filename = "$dir/$slug.json";
            $file = fopen($filename, 'w'); // make file

            $url = "https://academy.prod.sofascore.com/sport/$slug/tournaments";
            $response = $this->client->request('GET', $url);
            $data = json_decode($response->getContent(), true);
            $data2 = json_encode($data, JSON_PRETTY_PRINT);

            file_put_contents($filename, $data2); // write to file

            $this->messageBus->dispatch(new ParseFile($filename));
        }

        return self::SUCCESS;
    }

}