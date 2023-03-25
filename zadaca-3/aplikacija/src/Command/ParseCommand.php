<?php

declare(strict_types=1);

namespace App\Command;

use App\Database\Connection;
use App\Tools\JsonParser;
use App\Tools\XmlFeedParser;
use SimpleFW\Console\CommandInterface;
use SimpleFW\Console\Input;
use SimpleFW\Console\Output;

function insert_into_database($connection, $data) {
    $sportId = $connection->insert('sport', [
        'external_id' => $data->id,
        'name' => $data->name,
        'slug' => $data->slug,
    ]);

    foreach ($data->tournaments as $tournament) {
        $tournamentId = $connection->insert('tournament', [
            'sport_id' => $sportId,
            'external_id' => $tournament->id,
            'name' => $tournament->name,
            'slug' => $tournament->slug,
        ]);

        foreach ($tournament->events as $event) {
            $connection->insert('event', [
                'tournament_id' => $tournamentId,
                'external_id' => $event->id,
                'home_team_id' => $event->home_team_id,
                'away_team_id' => $event->away_team_id,
                'start_date' => $event->start_date->format('Y-m-d H:i:s.u e'),
                'home_score' => $event->home_score,
                'away_score' => $event->away_score,
            ]);
        }
    }
}

final class ParseCommand implements CommandInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function execute(Input $input, Output $output): int
    {
        if (!$input->hasArgument(1)) {
            $output->writeln('Missing required argument file name.');
            return self::FAILURE;
        }

        $filename = $input->getArgument(1);
        $dir = getcwd();
        $filepath = $dir.'/data/'.$filename;

        if (file_exists($filepath)) {
            $filetype = mime_content_type($filepath);
            $content = file_get_contents($filepath);

            switch($filetype) {
                case 'application/json':
                    $jsonParser = new JsonParser();
                    $jsonParser = $jsonParser->parse($content);
                    insert_into_database($this->connection, $jsonParser);
                    break;
    
                case 'text/xml':
                    $xmlParser = new XmlFeedParser();
                    $xmlParser = $xmlParser->parse($content);
                    insert_into_database($this->connection, $xmlParser);
                    break;
    
                default:
                    $output->writeln('Unsupported file type.');
                    return self::FAILURE;
            }

            $output->writeln('Successfully inserted into database.');
            return self::SUCCESS;
        } 

        $output->writeln('File not found.');
        return self::FAILURE;
    }
}
