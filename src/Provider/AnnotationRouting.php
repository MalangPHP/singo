<?php

namespace Singo\Provider;

use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Singo\Application;

/**
 * Class AnnotationRouting
 * @package Singo\Provider
 */
class AnnotationRouting implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container->register(new AnnotationServiceProvider());

        $container['annot.registerServiceController'] = $container->protect(
            function ($controllerName) use ($container) {
                if ($container['annot.useServiceControllers']) {
                    $container["$controllerName"] = function (Application $app) use ($controllerName) {
                        /**
                         * resolve dependency via container
                         */
                        $reflection_class = new \ReflectionClass($controllerName);
                        $params = $reflection_class->getConstructor()->getParameters();
                        $ctor_param = [];

                        foreach ($params as $param) {
                            $class = $param->getClass();

                            if ($class === null) {
                                throw new \InvalidArgumentException("`{$param->getName()}` in {$controllerName}
                                 constructor must have class type hinting");
                            }

                            $param_class_name = $class->getName();

                            if (! isset($app[$param_class_name])) {
                                throw new \InvalidArgumentException("{$param_class_name} in {$controllerName}
                                 not found in container");
                            }

                            array_push($ctor_param, $app[$param_class_name]);
                        }

                        return $reflection_class->newInstanceArgs($ctor_param);
                    };
                }
            }
        );
    }
}
