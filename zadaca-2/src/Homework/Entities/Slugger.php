<?php

namespace Sofa\Homework\Entities;

class Slugger {
	public function slugify($stringToSlug) {
		$sluggedString = '';
		
		// ne ASCII -> ASCII varijanta
		$sluggedString = iconv("UTF-8", "ASCII//TRANSLIT", $stringToSlug);
			
		// uklonit sve osim slova i brojeva
		$sluggedString = preg_replace('/[^a-zA-Z0-9]/i', ' ', $sluggedString);
		
		// razmak -> -
		$sluggedString = preg_replace('/\s+/', '-', $sluggedString);
		
		// maknut - s pocetak i s kraja 
		if ($sluggedString[0] === '-') {
			$sluggedString = substr($sluggedString, 1, strlen($sluggedString));
		} 
		if ($sluggedString[-1] === '-') {
			$sluggedString = substr($sluggedString, 0, strlen($sluggedString)-1);
		}
		
		// sve u mala slova 
		$sluggedString = strtolower($sluggedString);
		
		return "$sluggedString\n";
	}
}