<?php
namespace Sofa\Homework\Entities;
use Sofa\Homework\Entities\Slugger;
use DateTimeImmutable;
use SimpleXMLElement;


class XmlFeedParser {
	public function parse($xml) {
		
		$obj = new SimpleXMLElement($xml);
		
		$slugger = new Slugger();
		
		// sport
		$sportName = $obj->Name;
		$sportSlug = $slugger->slugify($sportName);
		$sportId = $obj->Id;
		$sportTournaments = array();
		
		
		// tournament 
		foreach($obj->Tournaments as $tournamentsJson) {
			$tournamentName = $tournamentsJson->Name;
			$tournamentSlug = $slugger->slugify($tournamentName);
			$tournamentId = $tournamentsJson->Id;
			$tournamentEvents = array();
			
			foreach($tournamentsJson->Events as $eventJson) {
				$eventId = $eventJson->Id;
				$eventHomeTeamId = $eventJson->HomeTeamId;
				$eventAwayTeamId = $eventJson->AwayTeamId;
				$eventStartDate = new DateTimeImmutable($eventJson->StartDate);
				$eventHomeScore = isset($eventJson->HomeScore) ? (int) $eventJson->HomeScore : null;
				$eventAwayScore = isset($eventJson->AwayScore) ? (int) $eventJson->AwayScore : null;
				
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