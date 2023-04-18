<?php

namespace CSVAPI\Repository;

interface QueryBuilder
{
    public function query(array $params): array;
}