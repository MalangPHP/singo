<?php

namespace Singo\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Silex\Provider\SecurityJWTServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class Firewall
 * @package Singo\Provider
 */
class Firewall implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container["security.jwt"] = $container["config"]->get("jwt");
        $container->register(new SecurityJWTServiceProvider());
        $container->register(new SecurityServiceProvider());
        $container["security.firewalls"] = $container["config"]->get("firewall");

        /**
         * alias for auto injection
         */
        $container[SecurityContext::class] = function () use ($container) {
            return $container["security"];
        };
    }
}
