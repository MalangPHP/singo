<?php


namespace Singo\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Singo\Config;
use Silex\Application;

/**
 * Class ConfigServiceProvider
 * @package Singo\Providers
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container["singo.config"] = function() use ($container) {
            return new Config($container);
        };
    }
}

// EOF
