<?php

namespace Singo;

use Pimple\Container;
use Silex\Provider\CacheServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Singo\Provider\CommandBus;
use Singo\Provider\Config;
use Singo\Provider\ContainerAwareEventDispatcher;
use Singo\Provider\Firewall;
use Singo\Provider\Logger;
use Singo\Provider\Mailer;
use Singo\Provider\Orm;
use Singo\Provider\Validator;

/**
 * Class ServiceInitializator
 * @package Singo
 */
trait ServiceInitializator
{
    /**
     * @var Application
     */
    public static $container;

    /**
     * @param Container $container
     */
    public function init(Container $container)
    {
        /**
         * container aware event dispatcher
         */
        $container->register(new ContainerAwareEventDispatcher());

        /**
         * cache
         */
        $container->register(new CacheServiceProvider());

        /**
         * configuration
         */
        $container->register(new Config());

        /**
         * logger
         */
        $container->register(new Logger());

        /**
         * doctrine orm
         */
        $container->register(new Orm());

        /**
         * validator
         */
        $container->register(new Validator());

        /**
         * mailer
         */
        $container->register(new Mailer());

        /**
         * command bus
         */
        $container->register(new CommandBus());

        /**
         * security
         */
        $container->register(new Firewall());

        /**
         * controller as a service
         */
        $container->register(
            new ServiceControllerServiceProvider(),
            [
                "tactician.inflector" => "class_name",
                "tactician.middlewares" =>
                    [
                        $container["command.bus.logger.middleware"],
                        $container["command.bus.validation.middleware"]
                    ]
            ]
        );

        /**
         * Save container in static variable
         */
        self::$container = $container;

        /**
         * boot module if module feature enabled in configuration
         */
        if ($container->offsetExists("use.module") && $container["use.module"] === true) {
            $this->bootModule();
        }
    }
}
