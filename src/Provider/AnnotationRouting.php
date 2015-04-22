<?php

namespace Singo\Provider;

use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Singo\Application;

class AnnotationRouting implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->register(new AnnotationServiceProvider());

        $container['annot.registerServiceController'] = $container->protect(
            function ($controllerName) use ($container) {
                if ($container['annot.useServiceControllers']) {
                    $container["$controllerName"] = function (Application $app) use ($controllerName) {
                        return new $controllerName($app["command.bus"]);
                    };
                }
            }
        );
    }
}
