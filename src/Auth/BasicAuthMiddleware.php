<?php

namespace CSVAPI\Auth;

use CSVAPI\Middleware\RoutePattern;
use CSVAPI\Repository\Repository;

class BasicAuthMiddleware extends AuthMiddleware
{
    private const BEARER = "Bearer";

    public function middleware_function(): void
    {
        $headers = getallheaders();
        if (array_key_exists('authorization', $headers)) {
            $auth = explode(" ", $headers['authorization']);
            if ($auth[0] != self::BEARER && $auth['1'] != $_ENV['api_key']) {
                Repository::respond403();
            }
        } else {
            Repository::respond401();
        }
    }

    public static function of(RoutePattern ...$patterns): BasicAuthMiddleware
    {
        return new BasicAuthMiddleware($patterns);
    }
}