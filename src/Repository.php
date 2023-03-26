<?php

namespace CSVAPI;

abstract class Repository
{
    public abstract function get(?string $index): array;

    public abstract function post(?string $index): array;

    public abstract function put(?string $index): array;

    public abstract function upsert(?string $index): array;

    public abstract function delete(string $index): array;
}