<?php

namespace CSVAPI\Repository;


use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\UnableToProcessCsv;

class DefaultRepository extends Repository implements QueryBuilder
{
    private CSVDB $csvdb;

    public function __construct(string $csv_file, CSVConfig $csv_config)
    {
        try {
            $this->csvdb = new CSVDB($csv_file, $csv_config);
        } catch (\Exception $e) {
            self::respond500($e);
        }
        $this->query_builder = $this;
    }

    public function get(?string $index = null): void
    {
        $params = self::get_parameter();
        $query = $this->csvdb->select();
        $where = [];
        if (!empty($index)) {
            $where = [$this->csvdb->index => $index];
        } else if (!empty($params)) {
            $where = $this->param_query();
        }

        if (!empty($where)) {
            $query = $query->where($where);
        }
        self::respond200($query->get());
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

    // Default QueryBuilder

    public function query(array $params): array
    {
        $where = [];
        foreach ($params as $key => $value) {
            $where[] = [$key => $value];
        }
        if (count($where) == 1) {
            $where = $where[0];
        }
        return $where;
    }
}