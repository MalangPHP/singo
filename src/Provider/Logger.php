<?php

namespace Singo\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\MonologServiceProvider;
use Monolog\Logger as Monolog;

/**
 * Class Logger
 * @package Singo\Provider
 */
class Logger implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $date = new \DateTime();
        $log_file = APP_PATH . $container["config"]->get("common/log/dir") . "/{$date->format("Y-m-d")}.log";
        $container->register(
            new MonologServiceProvider(),
            [
                "monolog.logfile" => $log_file,
                "monolog.name" => $container["config"]->get("common/log/name"),
                "monolog.level" => Monolog::INFO
            ]
        );
    }
}
