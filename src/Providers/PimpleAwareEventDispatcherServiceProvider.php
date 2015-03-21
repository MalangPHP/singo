<?php


namespace Singo\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Singo\Event\PimpleAwareEventDispatcher;

/**
 * Class PimpleAwareEventDispatcherServiceProvider
 * @package Singo\Providers
 */
class PimpleAwareEventDispatcherServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container["dispatcher"] = function () use ($container) {
            return new PimpleAwareEventDispatcher($container);
        };
    }
}

// EOF
