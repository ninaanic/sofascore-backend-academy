<?php

declare(strict_types=1);

namespace App\Command;
use App\Database\Connection;
use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Tournament;
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
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;


#[AsCommand(
    name: 'app:get:data',
    description: 'Get sport data from Provider.',
)]
final class GetDataFromProviderCommand extends Command
{

    public function __construct(
        private readonly MessageBus $messageBus,
        private readonly HttpClientInterface $client,
    ) {
        parent::__construct();    
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        
        # get all sports
        $sports = $this->load_json("sports");
        $this->save_json("sports", $sports);

        # get all tournaments for all sports
        $tournaments = [];
        foreach ($sports as $sport) {
            $sport_slug = $sport["slug"];
            $tournaments = array_merge($tournaments, $this->load_json("sport/$sport_slug/tournaments"));
        }
        $this->save_json("tournaments", $tournaments);

        # get all pages of events for a tournament
        $events = [];
        foreach ($tournaments as $tournament) {
            $tournament_id = $tournament["id"];
            $events = array_merge($events, $this->tournament_events($tournament_id, "last"));
            $events = array_merge($events, $this->tournament_events($tournament_id, "next"));
        }
        $this->save_json("events", $events);

        # prepare team_ids from events
        $team_ids = [];
        foreach ($events as $event) {
            array_push($team_ids, $event["homeTeam"]["id"]);
            array_push($team_ids, $event["awayTeam"]["id"]);
        }
        $team_ids_unique = array_unique($team_ids);
        sort($team_ids_unique);

        # get all teams
        $teams = [];
        foreach ($team_ids_unique as $team_id) {
            array_push($teams, $this->load_json("team/$team_id"));
        }
        $this->save_json("teams", $teams);

        # get all players
        $players = [];
        foreach($team_ids_unique as $team_id) {
            $players = array_merge($players, $this->load_json("team/$team_id/players"));
        }
        $this->save_json("players", $players);

        return self::SUCCESS;
    }

    public function load_json (string $path): array {

        $root = "https://academy.prod.sofascore.com";
        $url = "$root/$path";
        
        echo "loading $url\n";

        $response = $this->client->request('GET', $url);
        $data = json_decode($response->getContent(), true);

        return $data;

    }

    public function save_json (string $filename, array $data) {

        $json_data = json_encode($data, JSON_PRETTY_PRINT);

        $file = "data/" . $filename . ".json";
        file_put_contents($file, $json_data);

        $this->messageBus->dispatch(new ParseFile($file));
    }

    public function tournament_events (int $tournament_id, string $direction): array {
        $events = [];
        $page = 0;

        while (true) {
            $page_events = $this->load_json("tournament/$tournament_id/events/$direction/$page");

            if (empty($page_events)) {
                break;
            } 

            $events = array_merge($events, $page_events);
            $page += 1;
        }

        return $events;
    }

}