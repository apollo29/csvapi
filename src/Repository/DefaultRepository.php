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

    public function __construct(string $csv_file, CSVConfig $csv_config)
    {
        try {
            $this->csvdb = new CSVDB($csv_file, $csv_config);
        } catch (\Exception $e) {
            self::respond500($e);
        }
    }

    public function get(?string $index = null): void
    {
        if (empty($index)) {
            self::respond200($this->csvdb->select()->get());
        } else {
            self::respond200($this->csvdb->select()->where([$this->csvdb->index => $index])->get());
        }
    }

    public function post(array $data, ?string $index = null): void
    {
        if (empty($index)) {
            try {
                self::respond201($this->csvdb->insert($data));
            } catch (CannotInsertRecord $e) {
                self::respond400($e);
            } catch (InvalidArgument|Exception|UnableToProcessCsv $e) {
                self::respond500($e);
            }
        } else {
            try {
                self::respond201($this->csvdb->update($data, [$this->csvdb->index => $index]));
            } catch (CannotInsertRecord|\Exception $e) {
                self::respond400($e);
            } catch (UnableToProcessCsv $e) {
                self::respond500($e);
            }
        }
    }

    public function put(array $data, ?string $index = null): void
    {
        if (empty($data)) {
            try {
                self::respond201($this->csvdb->upsert($data));
            } catch (CannotInsertRecord|\Exception $e) {
                self::respond400($e);
            } catch (UnableToProcessCsv $e) {
                self::respond500($e);
            }
        } else {
            try {
                self::respond201($this->csvdb->upsert([$this->csvdb->index => $index]));
            } catch (CannotInsertRecord|\Exception $e) {
                self::respond400($e);
            } catch (UnableToProcessCsv $e) {
                self::respond500($e);
            }
        }
    }

    public function delete(string $index): void
    {
        try {
            $delete = $this->csvdb->delete([$this->csvdb->index => $index]);
            if ($delete) {
                self::respond204();
            } else {
                self::respond400("resource could not be deleted");
            }
        } catch (InvalidArgument|Exception $e) {
            self::respond500($e);
        }
    }
}