<?php

namespace CSVAPI;

use Bramus\Router\Router;
use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use Selective\ArrayReader\ArrayReader;

class CSVAPI
{

    private CSVDB $csvdb;
    private Router $router;

    public string $basedir;
    public string $csvfile;

    /**
     * @param string $csvfile
     * @param array $csvconfig
     * @param string $basedir
     *
     * @throws \Exception
     */
    public function __construct(string $csvfile, array $csvconfig, string $basedir = __DIR__)
    {
        $this->csvfile = $csvfile;
        $this->basedir = $basedir;

        $this->router = new Router();
        $this->csvdb = new CSVDB($this->csv_file(), $this->csv_config($csvconfig));
    }

    private function csv_file(): string
    {
        $basedir = $this->basedir;
        $file = $this->csvfile;
        // todo check for slash in the end
        //if (substr($basedir,-1))
        return $basedir . $file;
    }

    private function csv_config(array $csvconfig): CSVConfig
    {
        if (count($csvconfig) > 0) {
            $reader = new ArrayReader($csvconfig);

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
}