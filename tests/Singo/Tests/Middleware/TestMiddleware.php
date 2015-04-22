<?php

namespace Singo\Tests\Middleware;

use Singo\Application;
use Singo\Contracts\Middleware\ApplicationMiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestMiddleware implements ApplicationMiddlewareInterface
{
    private $app;

    public function __construct(Application $app, array $options = [])
    {
        $this->app = $app;
    }

    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return new Response("hello from middleware");
    }
}
