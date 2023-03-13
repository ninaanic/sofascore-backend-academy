#!/usr/bin/env php
<?php

require_once '../src/Homework/Autoloader.php';
use Sofa\Homework\Autoloader;
use Sofa\Homework\Entities\JsonParser;
use Sofa\Homework\Entities\XmlFeedParser;

Autoloader::register();

$dsn = 'pgsql:host=localhost;port=5433;dbname=zadaca-2';
$connection = new PDO($dsn.';user=postgres;password=nina');

function insert_into_database($connection, $data) {
    $sportName = $data->name;
    $sportSlug = $data->slug;
    $sportExternalId = $data->id;

    $stmt = $connection->prepare("INSERT INTO Sport(name, slug, external_id) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $sportName);
    $stmt->bindParam(2, $sportSlug);
    $stmt->bindParam(3, $sportExternalId, PDO::PARAM_STR);
    $stmt->execute();

    $sport_id = $connection->lastInsertId();

    foreach($data->tournaments as $tournamentsJson) {
        $tournamentName = $tournamentsJson->name;
		$tournamentSlug =$tournamentsJson->slug;
		$tournamenExternalId = $tournamentsJson->id;

        $stmt = $connection->prepare("INSERT INTO Tournament(name, slug, external_id, sport_id) VALUES (?, ?, ?, ?)");
        $stmt->bindParam(1, $tournamentName);
        $stmt->bindParam(2, $tournamentSlug);
        $stmt->bindParam(3, $tournamenExternalId, PDO::PARAM_STR);
        $stmt->bindParam(4, $sport_id);
        $stmt->execute();

        $tournament_id = $connection->lastInsertId();

        foreach($tournamentsJson->events as $eventJson) {
            $eventExternalId = $eventJson->id;
            $eventHomeTeamId = $eventJson->home_team_id;
            $eventAwayTeamId = $eventJson->away_team_id;

            $eventStartDate = $eventJson->start_date;
            $timestamp = $eventStartDate->format('Y-m-d H:i:s.u e');

            $eventHomeScore = $eventJson->home_score;
            $eventAwayScore = $eventJson->away_score;

            $stmt = $connection->prepare("INSERT INTO Event(external_id, home_team_id, away_team_id, start_date, home_score, away_score, tournament_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $eventExternalId, PDO::PARAM_STR);
            $stmt->bindParam(2, $eventHomeTeamId, PDO::PARAM_STR);
            $stmt->bindParam(3, $eventAwayTeamId, PDO::PARAM_STR);
            $stmt->bindParam(4, $timestamp);
            $stmt->bindParam(5, $eventHomeScore);
            $stmt->bindParam(6, $eventAwayScore);
            $stmt->bindParam(7, $tournament_id);
            $stmt->execute();
        }
    }
}


$filename = isset($argv[1]) ? $argv[1] : null;
if ($filename !== null) {
    $filepath = '../data/'.$filename;

    if (file_exists($filepath)) {
        $filetype = mime_content_type($filepath);
        $content = file_get_contents($filepath);

        switch($filetype) {
            case 'application/json':
                $jsonParser = new JsonParser();
                $jsonParser = $jsonParser->parse($content);
                insert_into_database($connection, $jsonParser);
                break;

            case 'text/xml':
                $xmlParser = new XmlFeedParser();
                $xmlParser = $xmlParser->parse($content);
                insert_into_database($connection, $xmlParser);
                break;

            default:
                echo "Unsupported file type: {$filetype}\n";
                exit(1);
        }

    } else {
        echo "File not found: {$filename}\n";
        exit(1);
    }
    
} else {
    echo "Plese enter filename as argument.\n";
    exit(1);
}