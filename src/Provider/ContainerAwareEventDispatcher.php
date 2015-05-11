<?php


namespace Singo\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\PimpleAwareEventDispatcherServiceProvider;
use Silex\PimpleAwareEventDispatcher;
use Singo\Application;
use Singo\Event\Listener\ExceptionHandler;

/**
 * Class ContainerAwareEventDispatcher
 * @package Singo\Provider
 */
class ContainerAwareEventDispatcher implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        if (! $container instanceof Application) {
            throw new \InvalidArgumentException("invalid application");
        }

        /**
         * pimple aware event dispatcher
         */
        $container->register(new PimpleAwareEventDispatcherServiceProvider());

        /**
         * alias for auto injection
         */
        $container[PimpleAwareEventDispatcher::class] = function () use ($container) {
            return $container["dispatcher"];
        };

        /**
         * register default event subscriber
         */
        $container->registerSubscriber(
            ExceptionHandler::class,
            function () use ($container) {
                return new ExceptionHandler($container["config"], $container["monolog"]);
            }
        );
    }
}
