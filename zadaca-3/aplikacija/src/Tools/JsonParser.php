<?php

namespace App\Tools;
use DateTimeImmutable;


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