<?php

namespace Singo\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Saxulum\DoctrineMongoDb\Provider\DoctrineMongoDbProvider;
use Saxulum\DoctrineMongoDbOdm\Provider\DoctrineMongoDbOdmProvider;

/**
 * Class Odm
 * @package Singo\Provider
 */
class Odm implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $this->register(
            new DoctrineMongoDbProvider,
            [
                "mongodb.options" => $container["config"]->get("database/connection/odm")
            ]
        );

        $this->register(
            new DoctrineMongoDbOdmProvider,
            [
                "mongodbodm.proxies_dir" => APP_PATH . $container["config"]->get("database/odm/proxies_dir"),
                "mongodbodm.proxies_namespace" => $container["config"]->get("database/odm/proxies_namespace"),
                "mongodbodm.auto_generate_proxies" => $container["config"]->get("database/odm/auto_generate_proxies"),
                "mongodbodm.dms.options" => $container["config"]->get("database/connection/odm")
            ]
        );
    }
}
