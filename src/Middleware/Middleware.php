<?php

namespace CSVAPI\Middleware;

use Bramus\Router\Router;

abstract class Middleware
{
    public string $methods;
    public string $pattern;

    /**
     * Store a before middleware route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     */
    public function __construct(string $methods, string $pattern)
    {
        $this->methods = $methods;
        $this->pattern = $pattern;
    }

    public function middleware(Router $router)
    {
        $router->before($this->methods, $this->pattern, call_user_func([$this, 'middleware_function']));
    }

    /**
     * The handling function to be executed
     */
    public abstract function middleware_function(): void;
}