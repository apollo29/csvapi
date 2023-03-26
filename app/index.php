<?php


use CSVAPI\CSVAPI;

require_once '../vendor/autoload.php';

$csvapi = new CSVAPI("games.csv", __DIR__);
$csvapi->run();
