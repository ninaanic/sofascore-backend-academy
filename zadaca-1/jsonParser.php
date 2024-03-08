<?php
include "slugger.php";

readonly class Sport
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $id,
        // @var Tournament[] 
        public array  $tournaments,
    ) {
    }
}

readonly class Tournament
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $id,
        // @var Event[] 
        public array  $events,
    ) {
    }
}

readonly class Event
{
    public function __construct(
        public string $id,
        public string $home_team_id,
        public string $away_team_id,
        public DateTimeImmutable $start_date,
        public ?int $home_score,
        public ?int $away_score
    ) {
    }
}

class JsonParser {
	public function parse($json) {
		
		$obj = json_decode($json);
		
		$slugger = new Slugger();
		
		// sport
		$sportName = $obj->{'name'};
		$sportSlug = $slugger->slugify($sportName);
		$sportId = $obj->{'id'};
		$sportTournaments = array();
		
		
		// tournament 
		foreach($obj->{'tournaments'} as $tournamentsJson) {
			$tournamentName = $tournamentsJson->{'name'};
			$tournamentSlug = $slugger->slugify($tournamentName);
			$tournamentId = $tournamentsJson->{'id'};
			$tournamentEvents = array();
			
			foreach($tournamentsJson->{'events'} as $eventJson) {
				$eventId = $eventJson->{'id'};
				$eventHomeTeamId = $eventJson->{'home_team_id'};
				$eventAwayTeamId = $eventJson->{'away_team_id'};
				$eventStartDate = new DateTimeImmutable($eventJson->{'start_date'});
				$eventHomeScore = $eventJson->{'home_score'};
				$eventAwayScore = $eventJson->{'away_score'};
				
				$event = new Event($eventId, $eventHomeTeamId, $eventAwayTeamId, $eventStartDate, $eventHomeScore, $eventAwayScore);
				
				$tournamentEvents[] = $event;
			}
			
			$tournament = new Tournament($tournamentName, $tournamentSlug, $tournamentId, $tournamentEvents);
			
			$sportTournaments[] = $tournament;
		}
		
		$sport = new Sport($sportName, $sportSlug, $sportId, $sportTournaments);
		
		return $sport;
	}
}


$json = <<<'EOT'
{
    "name": "Football",
    "id": "ba39480d-560d-4926-878d-1e79159c98e6",
    "tournaments": [
        {
            "name": "Trento, Doubles M-ITF-ITA-01A",
            "id": "302e9398-1427-4b0d-a839-f58785cec91e",
            "events": [
                {
                    "id": "3c3917ee-2fe8-48ff-bcc1-106c397878f6",
                    "home_team_id": "6be94059-7e94-460f-9ac6-dd7ab379bd61",
                    "away_team_id": "24944933-3c9e-4bda-92f1-8cfa78bed034",
                    "start_date": "2020-02-26 18:05:00",
                    "home_score": 2,
                    "away_score": 0
                },
                {
                    "id": "3c400e79-e6af-4786-8cb4-a96cc9460da3",
                    "home_team_id": "0cd906cb-79c6-4876-b3ad-51cbfc8b4cba",
                    "away_team_id": "bed48874-35be-4bbf-bb9c-8525bb8c3bd6",
                    "start_date": "2020-02-25 15:15:00",
                    "home_score": 0,
                    "away_score": 2
                },
                {
                    "id": "565ce91b-cd78-42df-94f0-d76346026f06",
                    "home_team_id": "0cd906cb-79c6-4876-b3ad-51cbfc8b4cba",
                    "away_team_id": "6dcc9e03-b4c6-4550-8715-43e235f8d6b5",
                    "start_date": "2018-07-10 13:10:00",
                    "home_score": 2,
                    "away_score": 1
                }
            ]
        },
        {
            "name": "Wimbledon, Boys, Doubles",
            "id": "a31f7e0f-821e-4300-ab8b-00b021fbf1b6",
            "events": [
                {
                    "id": "7713fec0-68b7-4ef1-b6dc-cb1af93760c0",
                    "home_team_id": "2ffc0f1a-1434-4892-a43b-e1c29e0764fd",
                    "away_team_id": "0cd906cb-79c6-4876-b3ad-51cbfc8b4cba",
                    "start_date": "2017-07-12 15:35:00",
                    "home_score": 1,
                    "away_score": 2
                }
            ]
        },
        {
            "name": "Italy F1, Doubles",
            "id": "3dfa1f61-9db2-4a49-a91f-2784565b7189",
            "events": [
                {
                    "id": "ea385a40-b492-4e05-b7fa-916845ca7002",
                    "home_team_id": "6dcc9e03-b4c6-4550-8715-43e235f8d6b5",
                    "away_team_id": "1366f4b3-2892-4024-8b2c-feddef80eea5",
                    "start_date": "2018-02-28 11:00:00",
                    "home_score": 2,
                    "away_score": 1
                }, 
                {
                    "id": "c3d25aa9-8c7d-4e74-8925-2ab48d8ce350",
                    "home_team_id": "2ffc0f1a-1434-4892-a43b-e1c29e0764fd",
                    "away_team_id": "bf7e8d2d-3732-446d-be1a-d744b7688275",
                    "start_date": "2020-11-25 20:00:00",
                    "home_score": null,
                    "away_score": null
                }
            ]
        }
    ]
}
EOT;

$jsonParser = new JsonParser();
var_dump($jsonParser->parse($json));