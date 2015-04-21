<?php

namespace Singo\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\ConfigServiceProvider;

/**
 * Class Config
 * @package Singo\Provider
 */
class Config implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container->register(new ConfigServiceProvider($container["config.path"]));
        $container["debug"] = $container["config"]->get("common/debug");
    }
}
