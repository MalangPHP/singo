<?php

namespace Singo\Contracts\Module;

use Singo\Application;

/**
 * Interface CommandHandlerProviderInterface
 * @package Singo\Contracts\Module
 */
interface CommandHandlerProviderInterface
{
    /**
     * @param Application $app
     * @return void
     */
    public function command(Application $app);
}
