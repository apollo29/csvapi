<?php

namespace CSVAPI\Repository;

abstract class Repository
{
    public abstract function get(?string $index = null): void;

    public abstract function post(array $data, ?string $index = null): void;

    public abstract function put(array $data, ?string $index = null): void;

    public abstract function delete(string $index): void;

    // PUT

    public static function get_put(): array
    {
        // Fake $_PUT
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        return $_PUT;
    }

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

    private static function output(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}