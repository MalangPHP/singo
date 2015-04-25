<?php

namespace Singo\Contracts\Module;

use Singo\Application as Singo;
use Symfony\Component\Console\Application as CLI;

/**
 * Interface CliCommandProviderInterface
 * @package Singo\Contracts\Module
 */
interface CliCommandProviderInterface
{
    /**
     * @param CLI $app
     * @param Singo $container
     * @return void
     */
    public function cli(CLI $app, Singo $container);
}
