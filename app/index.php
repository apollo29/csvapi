<?php


use CSVAPI\CSVAPI;

require_once '../vendor/autoload.php';
require_once 'TestMiddleware.php';

$csvapi = new CSVAPI("games.csv", __DIR__);
$csvapi->middleware(new TestMiddleware("GET",".*"));
$csvapi->run();
