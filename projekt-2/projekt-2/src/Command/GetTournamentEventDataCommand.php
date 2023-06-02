<?php

declare(strict_types=1);

namespace App\Command;
use App\Database\Connection;
use App\Message\ParseFile;
use App\Entity\Sport;
use App\Entity\Tournament;
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
    name: 'app:get:tournament:event:data',
    description: 'Get Event data for given Sport and Date from Provider.',
)]
final class GetTournamentEventDataCommand extends Command
{
    
    public function __construct(
        private readonly MessageBus $messageBus,
        private readonly HttpClientInterface $client,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int {

        $parent_dir = "data/Events/Tournament";

        // get sports
        $tournamentEntity = $this->entityManager->getRepository(Tournament::class)->findAll();
        
        foreach ($tournamentEntity as $tournament) {
            $id = $tournament->getId();

            $span = ["last", "next"];
            $page = 0;

            foreach ($span as $s) {
                $url = "https://academy.prod.sofascore.com/tournament/$id/events/$s/$page";
                $headers = @get_headers($url);
                
                // Use condition to check the existence of URL
                if($headers && strpos( $headers[0], '200')) {

                    $dir = "$parent_dir/$id";
                    if (!is_dir($dir)) {
                        mkdir($dir);
                    }

                    $spanPage = $s . strval($page);
                    $filename = "$dir/$spanPage.json";
                    $file = fopen($filename, 'w'); // make file

                    $response = $this->client->request('GET', $url);
                    $data = json_decode($response->getContent(), true);
                    $data2 = json_encode($data, JSON_PRETTY_PRINT);

                    file_put_contents($filename, $data2); // write to file

                    $this->messageBus->dispatch(new ParseFile($filename));
                }
            }
        }

        return self::SUCCESS;
    }

}