<?php

use Bramus\Router\Router;

require_once '../vendor/autoload.php';


// Create Router instance
$router = new Router();

// This route handling function will only be executed when visiting http(s)://www.example.org/about
$router->get('/', function() {
    header('Content-Type: application/json');
    echo json_encode(['data'=>'Home Page Contents']);
});

// This route handling function will only be executed when visiting http(s)://www.example.org/about
$router->get('/about', function() {
    echo 'About Page Contents';
});

// Run it!
$router->run();
