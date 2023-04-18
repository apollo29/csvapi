<?php

namespace CSVAPI\Middleware;

class RoutePattern
{
    public string $methods;
    public string $pattern;

    function __construct(string $methods, string $pattern)
    {
        $this->methods = $methods;
        $this->pattern = $pattern;
    }

    /**
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     */
    public static function of(string $methods, string $pattern): RoutePattern
    {
        return new RoutePattern($methods, $pattern);
    }
}