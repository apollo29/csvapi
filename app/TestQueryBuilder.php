<?php

use CSVAPI\Repository\QueryBuilder;
use CSVDB\CSVDB;

class TestQueryBuilder implements QueryBuilder
{

    public function query(array $params): array
    {
        $where = [];
        foreach ($params as $key => $value) {
            if ($key == "Teamname") {
                $where[] = [["Teamname A" => $value],["Teamname B" => $value], CSVDB::OR];
            } else {
                $where[] = [$key => $value];
            }
        }
        if (count($where) == 1) {
            $where = $where[0];
        }
        return $where;
    }
}