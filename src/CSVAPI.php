<?php

namespace CSVAPI;

use Bramus\Router\Router;
use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use Dotenv\Dotenv;
use Selective\ArrayReader\ArrayReader;

class CSVAPI
{

    private CSVDB $csvdb;

    public string $csv_file;
    public string $basedir;
    public string $baseroute;

    /**
     * @param string $csv_file
     * @param string $basedir
     * @throws \Exception
     */
    public function __construct(string $csv_file, string $basedir)
    {
        // load env
        $dotenv = Dotenv::createImmutable($basedir);
        $dotenv->load();

        $this->csv_file = $csv_file;
        $this->basedir = $this->basedir($basedir);
        $this->baseroute = $this->baseroute();

        $this->csvdb = new CSVDB($this->csv_file(), $this->csv_config($_ENV));
    }

    private function basedir(string $basedir): string
    {
        if (substr($basedir, -1) !== "/" || substr($basedir, -1) !== "\\") {
            $basedir = $basedir . "/";
        }
        return $basedir;
    }

    private function baseroute(): string
    {
        if (!isset($_ENV['baseroute'])) {
            $path_parts = pathinfo($this->csv_file());
            return strtolower($path_parts['filename']);
        } else {
            return $_ENV['baseroute'];
        }
    }

    private function csv_file(): string
    {
        return $this->basedir . $this->csv_file;
    }

    private function csv_config(array $config): CSVConfig
    {
        if (!empty($config) > 0) {
            $reader = new ArrayReader($config);

            $index = $reader->findInt('index', CSVConfig::INDEX);
            $encoding = $reader->findString('encoding', CSVConfig::ENCODING);
            $delimiter = $reader->findString('delimiter', CSVConfig::DELIMITER);
            $headers = $reader->findBool('headers', CSVConfig::HEADERS);
            $cache = $reader->findBool('cache', CSVConfig::CACHE);
            $history = $reader->findBool('history', CSVConfig::HISTORY);
            $autoincrement = $reader->findBool('autoincrement', CSVConfig::AUTOINCREMENT);

            return new CSVConfig($index, $encoding, $delimiter, $headers, $cache, $history, $autoincrement);
        }
        return CSVConfig::default();
    }

    public function run()
    {
        // todo basic CRUD operations can be configured if applicable or not  (or override) ==> repository class (interface) see: https://github.com/bramus/router#classmethod-calls
        // todo add custom routes
        // todo output always with header json ==> CSVDB add answer!!
        // todo add middleware with auth! (basic auth or create own)
        // todo whatabout params for "where" statements... (config)
        // todo check for data (post,put) -> https://github.com/bramus/router#before-route-middlewares
        // todo index == index of csv config! or override
        // todo make "single file" with .env config (index.php, .env, .htaccess and corresponding csv file)
        // todo return proper status codes: https://pavledjuric.medium.com/best-practices-for-designing-rest-apis-using-proper-status-codes-461fde1cbb1c

        $router = new Router();
        $csvdb = $this->csvdb;

        // home
        $router->get('/', function () use ($csvdb) {
            echo "<h1>csvapi</h1>";
        });

        // api
        $router->mount('/' . $this->baseroute, function () use ($router, $csvdb) {
            $router->before("PUT|POST", "*", function () {
                self::before();
            });

            $router->before("PUT|POST", "/.*", function () {
                self::before();
            });

            // READ
            $router->get('/', function () use ($csvdb) {
                $data = $csvdb->select()->get();
                self::output($data);
            });

            $router->get('/{index}', function ($index) use ($csvdb) {
                $data = $csvdb->select()->where([$csvdb->index => $index])->get();
                self::output($data);
            });

            // CREATE
            $router->post('/', function () use ($csvdb) {
                $create = $csvdb->insert($_POST);
                self::output($create);
            });

            // UPDATE
            $router->post('/{index}', function ($index) use ($csvdb) {
                $update = $csvdb->update($_POST, [$csvdb->index => $index]);
                self::output($update);
            });

            // UPSERT
            $router->put('/', function () use ($router, $csvdb) {
                $_PUT = self::put();

                $upsert = $csvdb->upsert($_PUT);
                self::output($upsert);
            });

            $router->put('/{index}', function ($index) use ($csvdb) {
                $_PUT = self::put();

                $upsert = $csvdb->upsert($_PUT, [$csvdb->index => $index]);
                self::output($upsert);
            });

            // DELETE
            $router->delete('/{index}', function ($index) use ($csvdb) {
                $delete = $csvdb->delete([$csvdb->index => $index]);
                if ($delete) {
                    Response::respond204();
                }
                else {
                    Response::respond400("resource could not be deleted");
                }
            });
        });

        $router->run();
    }

    private static function before(): void
    {
        $data = $_POST;
        if ($_SERVER['REQUEST_METHOD'] == "PUT") {
            $data = self::put();
        }

        if (empty($data)) {
            Response::respond400("empty body");
            exit;
        }
    }

    private static function put(): array
    {
        // Fake $_PUT
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        return $_PUT;
    }

    private static function output($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}