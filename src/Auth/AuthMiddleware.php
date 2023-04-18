<?php

namespace CSVAPI\Auth;

use Bramus\Router\Router;
use CSVAPI\Middleware\RoutePattern;

abstract class AuthMiddleware
{
    private array $patterns;

    /**
     * @param array $patterns Allowed methods with | delimited and a route pattern
     */
    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
    }

    public function middleware(Router $router)
    {
        foreach ($this->patterns as $pattern) {
            if ($pattern instanceof RoutePattern) {
                $router->before($pattern->methods, $pattern->pattern, call_user_func([$this, 'middleware_function']));
            }
        }
    }
}