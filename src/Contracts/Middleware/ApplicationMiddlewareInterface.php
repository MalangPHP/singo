<?php

namespace Singo\Contracts\Middleware;

use Singo\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Interface ApplicationMiddlewareInterface
 * @package Singo\Contracts\Middleware
 */
interface ApplicationMiddlewareInterface extends HttpKernelInterface
{
    /**
     * @param Application $app
     */
    public function __construct(Application $app);
}
