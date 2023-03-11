<?php

require_once '../Autoloader.php';
use Sofa\Homework\Autoloader;
use Sofa\Homework\Entities\Slugger;

Autoloader::register();

$slugger = new Slugger();
echo $slugger->slugify('Ovo je neki tekst sa šđčćž');
echo $slugger->slugify('Ovaj \ (tekst) =ima $ neke_znakove / koji #nisu +slova!');
echo $slugger->slugify('Ovaj tekst ima ---- u sebi.');
echo $slugger->slugify('- Ovaj tekst ima - na početku i kraju.-');
echo $slugger->slugify('Ovaj je 1. tekst koji ma brojeve u sebi, npr 12 37 4.');