<?php


namespace Singo\Bus;

use League\Tactician\Command;
use League\Tactician\Handler\Locator\HandlerLocator;
use Pimple\Container;
use Silex\Application;

/**
 * Class SilexLocator
 * @package Singo\Bus
 */
class SilexLocator implements HandlerLocator
{
    /**
     * @var Application
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Command $command
     * @return object
     */
    public function getHandlerForCommand(Command $command)
    {
        $handler_id = "app.handler." . join("", array_slice(explode("\\", get_class($command)), -1));
        return $this->container[$handler_id];
    }
}

// EOF
