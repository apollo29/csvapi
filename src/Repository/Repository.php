<?php

namespace CSVAPI\Repository;

abstract class Repository
{
    public QueryBuilder $query_builder;

    public abstract function get(?string $index = null): void;

    public abstract function post(array $data, ?string $index = null): void;

    public abstract function put(array $data, ?string $index = null): void;

    public abstract function delete(string $index): void;

    // URL Parameter

    public static function get_parameter(): array
    {
        $parameters = [];
        if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
            $query_string = $_SERVER['REDIRECT_QUERY_STRING'];
            $allowed = self::allowed_parameters();
            $queries = explode("&", $query_string);
            foreach ($queries as $query) {
                $values = explode("=", $query);
                if (empty($allowed) || in_array(rawurldecode($values[0]), $allowed)) {
                    $parameters[rawurldecode($values[0])] = rawurldecode($values[1]);
                }
            }
        }
        return $parameters;
    }

    public static function allowed_parameters(): array
    {
        $allowed_parameters = [];
        if (isset($_ENV['allowed_parameters'])) {
            $allowed_parameters = explode(",", $_ENV['allowed_parameters']);
        }
        return $allowed_parameters;
    }

    // Query Builder

    public function query_builder(QueryBuilder $query_builder): void
    {
        $this->query_builder = $query_builder;
    }

    public function param_query(): array
    {
        if (!empty(self::allowed_parameters())) {
            if (!empty($this->query_builder)) {
                return $this->query_builder->query(self::get_parameter());
            }
        }
        return [];
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
        exit;
    }

    public static function respond403(): void
    {
        http_response_code(403);
        exit;
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