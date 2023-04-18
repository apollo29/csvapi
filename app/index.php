<?php


use CSVAPI\CSVAPI;

require_once '../vendor/autoload.php';
require_once 'TestQueryBuilder.php';

$csvapi = new CSVAPI("games.csv", __DIR__);
$csvapi->query_builder(new TestQueryBuilder());
$csvapi->run();
?>
