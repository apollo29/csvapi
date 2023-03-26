<?php

use CSVAPI\Middleware\Middleware;

class TestMiddleware extends Middleware
{

    public function middleware_function(): void
    {
        var_dump("TEST MIDDLEWARE");
        var_dump(getallheaders());
    }
}