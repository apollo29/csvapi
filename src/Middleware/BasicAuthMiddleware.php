<?php

namespace CSVAPI\Middleware;

class BasicAuthMiddleware extends Middleware
{

    public function middleware_function(): void
    {
        // TODO: Implement middleware_function() method.
        var_dump(getallheaders());
        //["authorization"]=>
        //  string(11) "Bearer test"
    }
}