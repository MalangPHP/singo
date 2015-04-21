<?php

namespace Singo\Provider;

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\DoctrineServiceProvider;

/**
 * Class Orm
 * @package Singo\Provider
 */
class Orm implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container->register(
            new DoctrineServiceProvider(),
            [
                "dbs.options" => $container["config"]->get("database/connection/orm")
            ]
        );

        $container->register(
            new DoctrineOrmServiceProvider(),
            [
                "orm.proxies_dir" => APP_PATH . $container["config"]->get("database/orm/proxies_dir"),
                "orm.proxies_namespace" => $container["config"]->get("database/orm/proxies_namespace"),
                "orm.ems.options" => $container["config"]->get("database/ems")
            ]
        );
    }
}
