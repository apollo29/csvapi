<?php

namespace CSVAPI\Repository;

abstract class Repository
{
    public abstract function get(?string $index = null): void;

    public abstract function post(array $data, ?string $index = null): void;

    public abstract function put(array $data, ?string $index = null): void;

    public abstract function delete(string $index): void;

    // Response

    public static function respond200(array $data): void
    {
        http_response_code(200);
        self::output($data);
    }

    public static function respond201(array $data): void
    {
        http_response_code(201);
        self::output($data);
    }

    public static function respond204(): void
    {
        http_response_code(204);
    }

    public static function respond400(string $message): void
    {
        http_response_code(400);
        self::output(["error" => $message]);
    }

    public static function respond401(): void
    {
        http_response_code(401);
    }

    public static function respond403(): void
    {
        http_response_code(403);
    }

    public static function respond404(): void
    {
        http_response_code(404);
    }

    public static function respond500(string $message): void
    {
        http_response_code(500);
        self::output(["error" => $message]);
    }

    private static function output(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}