<?php

use Bramus\Router\Router;
use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;

require_once '../vendor/autoload.php';

$csvfile = __DIR__ . "/games.csv";
$config = new CSVConfig(1, "UTF-8", ";", true, true, false);
$csvdb = new CSVDB($csvfile, $config);

$router = new Router();

$router->mount('/games', function() use ($router, $csvdb) {

    $router->get('/', function() use ($csvdb) {
        header('Content-Type: application/json');
        echo json_encode($csvdb->select()->get());
    });

    $router->get('/{spielnummer}', function($spielnummer) use ($csvdb) {
        header('Content-Type: application/json');
        echo json_encode($csvdb->select()->where(["Spielnummer"=>$spielnummer])->get());
    });
});

$router->get('/', function () {
    header('Content-Type: application/json');
    echo json_encode(['data' => 'Home Page Contents']);
});

$router->run();
