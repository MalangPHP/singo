<?php

namespace Singo\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\SwiftmailerServiceProvider;

/**
 * Class Mailer
 * @package Singo\Provider
 */
class Mailer implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container["swiftmailer.options"] = $container["config"]->get("mailer");
        $container->register(new SwiftmailerServiceProvider());
    }
}
