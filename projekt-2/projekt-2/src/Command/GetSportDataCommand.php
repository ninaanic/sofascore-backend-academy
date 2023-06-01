<?php

declare(strict_types=1);

namespace App\Command;
use App\Database\Connection;
use App\Message\ParseFile;
use App\Message\ProcessDataMessage;
use Doctrine\DBAL\ArrayParameters\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Handler\ParseSportFileHandler;

#[AsCommand(
    name: 'app:get:sport:data',
    description: 'Get sport data from Provider.',
)]
final class GetSportDataCommand extends Command
{

    public function __construct(
        private readonly MessageBus $messageBus,
        private readonly HttpClientInterface $client
    ) {
        parent::__construct();    
    }

    public function execute(InputInterface $input, OutputInterface $output): int {

        $file = 'data/sports.json';

        $url = "https://academy.prod.sofascore.com/sports";
        $response = $this->client->request('GET', $url);
        $data = json_decode($response->getContent(), true);
        $data2 = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents($file, $data2);
        $this->messageBus->dispatch(new ParseFile($file));

        
        
        return self::SUCCESS;
    }

}