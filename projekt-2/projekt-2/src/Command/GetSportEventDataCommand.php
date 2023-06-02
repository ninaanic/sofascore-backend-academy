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
    name: 'app:get:sport:event:data',
    description: 'Get Event data for given Sport and Date from Provider.',
)]
final class GetSportEventDataCommand extends Command
{
    
    public function __construct(
        private readonly MessageBus $messageBus,
        private readonly HttpClientInterface $client,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int {

        $parent_dir = "data/Events/SportDate";

        // get sports
        $sportEntity = $this->entityManager->getRepository(Sport::class)->findAll();
        
        foreach ($sportEntity as $sport) {
            $slug = $sport->getSlug();

            $dir = "$parent_dir/$slug";

            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $date = "2023-04-29";

            $filename = "$dir/$date.json";
            $file = fopen($filename, 'w'); // make file

            $url = "https://academy.prod.sofascore.com/sport/$slug/events/$date";
            $response = $this->client->request('GET', $url);
            $data = json_decode($response->getContent(), true);
            $data2 = json_encode($data, JSON_PRETTY_PRINT);

            file_put_contents($filename, $data2); // write to file

            $this->messageBus->dispatch(new ParseFile($filename));
        }

        return self::SUCCESS;
    }

}