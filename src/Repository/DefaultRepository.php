<?php

namespace CSVAPI\Repository;


use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\UnableToProcessCsv;

class DefaultRepository extends Repository
{

    private CSVDB $csvdb;

    /**
     * @throws \Exception
     */
    public function __construct(string $csv_file, CSVConfig $csv_config)
    {
        $this->csvdb = new CSVDB($csv_file, $csv_config);
    }

    public function get(?string $index = null): void
    {
        if (empty($index)) {
            self::respond200($this->csvdb->select()->get());
        } else {
            self::respond200($this->csvdb->select()->where([$this->csvdb->index => $index])->get());
        }
    }

    /**
     * @throws UnableToProcessCsv|InvalidArgument|CannotInsertRecord|Exception
     */
    public function post(array $data, ?string $index = null): void
    {
        if (empty($index)) {
            self::respond201($this->csvdb->insert($data));
        } else {
            self::respond201($this->csvdb->update($data, [$this->csvdb->index => $index]));
        }
    }

    /**
     * @throws UnableToProcessCsv|InvalidArgument|CannotInsertRecord|Exception
     */
    public function put(array $data, ?string $index = null): void
    {
        if (empty($data)) {
            self::respond201($this->csvdb->upsert($data));
        } else {
            self::respond201($this->csvdb->upsert([$this->csvdb->index => $index]));
        }
    }

    /**
     * @throws InvalidArgument|Exception
     */
    public function delete(string $index): void
    {
        $delete = $this->csvdb->delete([$this->csvdb->index => $index]);
        if ($delete) {
            self::respond204();
        } else {
            self::respond400("resource could not be deleted");
        }
    }
}