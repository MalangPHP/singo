<?php


namespace Singo\Providers;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Singo\Bus\Middleware\CommandLoggerMiddleware;
use Singo\Bus\Middleware\CommandValidationMiddleware;
use Singo\Bus\SilexLocator;
use Silex\Application;

/**
 * Class CommandBusServiceProvider
 * @package Singo\Providers
 */
class CommandBusServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container["bus"] = function () use ($container) {
            $handlerMiddleware = new CommandHandlerMiddleware(
                new SilexLocator($container),
                new HandleClassNameInflector()
            );

            $loggerMiddleware = new CommandLoggerMiddleware($container["monolog"]);
            $validationMiddleware = new CommandValidationMiddleware($container["validator"], $container["monolog"]);
            return new CommandBus([$loggerMiddleware, $validationMiddleware, $handlerMiddleware]);
        };

    }
}

// EOF
