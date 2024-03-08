<?php
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


$slugger = new Slugger();
echo $slugger->slugify('Ovo je neki tekst sa šđčćž');
echo $slugger->slugify('Ovaj \ (tekst) =ima $ neke_znakove / koji #nisu +slova!');
echo $slugger->slugify('Ovaj tekst ima ---- u sebi.');
echo $slugger->slugify('- Ovaj tekst ima - na početku i kraju.-');
echo $slugger->slugify('Ovaj je 1. tekst koji ma brojeve u sebi, npr 12 37 4.');