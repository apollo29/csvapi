<?php

namespace CSVAPI;

class Response
{
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