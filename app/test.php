<pre>
<?php

use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use Dotenv\Dotenv;

require '../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

var_dump($_ENV);

$csvdb = new CSVDB(__DIR__ . "/games.csv", new CSVConfig(1, CSVConfig::ENCODING, ";", CSVConfig::HEADERS, CSVConfig::CACHE, CSVConfig::HISTORY, false));
$data = $csvdb->select()->where([$csvdb->index => "1"])->get();
var_dump($data);
?>
</pre>
