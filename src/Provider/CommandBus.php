<?php

namespace Singo\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\TacticianServiceProvider;
use Singo\Bus\Middleware\CommandLoggerMiddleware;
use Singo\Bus\Middleware\CommandValidationMiddleware;

/**
 * Class CommandBus
 * @package Singo\Provider
 */
class CommandBus implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container["command.bus.logger.middleware"] = function () use ($container) {
            return new CommandLoggerMiddleware($container["monolog"]);
        };

        $container["command.bus.validation.middleware"] = function () use ($container) {
            return new CommandValidationMiddleware($container["validator"], $container["monolog"]);
        };

        $container->register(new TacticianServiceProvider());
    }
}
