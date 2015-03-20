<?php


namespace Singo\Providers;

use League\Fractal\Manager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class FractalServiceProvider
 * @package Singo\Providers
 */
class FractalServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container["fractal"] = function() {
            return new Manager();
        };
    }
}

// EOF
