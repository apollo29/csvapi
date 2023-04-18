<?php

namespace CSVAPI;

use Bramus\Router\Router;
use CSVAPI\Auth\AuthMiddleware;
use CSVAPI\Middleware\Middleware;
use CSVAPI\Repository\DefaultRepository;
use CSVAPI\Repository\QueryBuilder;
use CSVAPI\Repository\Repository;
use CSVDB\Helpers\CSVConfig;
use Dotenv\Dotenv;
use Selective\ArrayReader\ArrayReader;

class CSVAPI
{

    private Repository $repository;

    public string $csv_file;
    public string $basedir;
    public string $baseroute;

    private array $auth = array();
    private array $middleware = array();

    /**
     * @param string $csv_file
     * @param string $basedir
     * @param Repository|null $repository
     */
    public function __construct(string $csv_file, string $basedir, Repository $repository = null)
    {
        $dotenv = Dotenv::createImmutable($basedir);
        $dotenv->load();

        $this->csv_file = $csv_file;
        $this->basedir = $this->basedir($basedir);
        $this->baseroute = $this->baseroute();

        $this->repository = $repository ?: new DefaultRepository($this->csv_file(), $this->csv_config($_ENV));
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

    // Repository

    public function query_builder(QueryBuilder $query_builder): void
    {
        $this->repository->query_builder($query_builder);
    }

    // MIDDLEWARE

    public function auth(AuthMiddleware ...$middlewares): void
    {
        foreach ($middlewares as $middleware) {
            $this->auth[get_class($middleware)] = $middleware;
        }
    }

    public function middleware(Middleware ...$middlewares): void
    {
        foreach ($middlewares as $middleware) {
            $this->middleware[get_class($middleware)] = $middleware;
        }
    }

    // RUN

    public function run(): void
    {
        // todo root route
        // todo add custom routes

        $router = new Router();
        $repository = $this->repository;

        // home
        $router->get('/', function () {
            echo "<h1>csvapi</h1>";
        });

        // api
        $router->mount('/' . $this->baseroute, function () use ($router, $repository) {

            // auth middleware
            foreach ($this->auth as $auth) {
                if ($auth instanceof AuthMiddleware) {
                    $auth->middleware_function();
                }
            }

            // custom before app middleware
            foreach ($this->middleware as $middleware) {
                if ($middleware instanceof Middleware) {
                    $middleware->middleware_function();
                }
            }

            // before app middleware of CSVAPI
            $router->before("PUT|POST", "*", function () {
                self::before();
            });

            $router->before("PUT|POST", "/.*", function () {
                self::before();
            });

            // READ
            $router->get('/', function () use ($repository) {
                $repository->get();
            });

            $router->get('/{index}', function ($index) use ($repository) {
                $repository->get($index);
            });

            // CREATE
            $router->post('/', function () use ($repository) {
                $repository->post($_POST);
            });

            // UPDATE
            $router->post('/{index}', function ($index) use ($repository) {
                $repository->post($_POST, $index);
            });

            // UPSERT
            $router->put('/', function () use ($router, $repository) {
                $repository->post(self::put());
            });

            $router->put('/{index}', function ($index) use ($repository) {
                $repository->post(self::put(), $index);
            });

            // DELETE
            $router->delete('/{index}', function ($index) use ($repository) {
                $repository->delete($index);
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
            Repository::respond400("empty body");
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
}