<?php

namespace Singo\Contracts\Module;

use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Singo\Application;

/**
 * Interface ModuleInterface
 * @package Singo\Contracts\Module
 */
interface ModuleInterface extends
    ServiceProviderInterface,
    BootableProviderInterface,
    EventListenerProviderInterface
{
    public function command(Application $app);
}
