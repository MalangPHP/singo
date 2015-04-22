<?php

namespace Singo\Provider;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Cache;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

/**
 * Class Validator
 * @package Singo\Provider
 */
class Validator implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container->register(new ValidatorServiceProvider());

        $container["validator.mapping.class_metadata_factory"] = function () use ($container) {
            $reader = new AnnotationReader();
            $loader = new AnnotationLoader($reader);

            $cache = $container->offsetExists("cache.factory") && $container["cache.factory"] instanceof Cache
                ? new DoctrineCache($container["cache.factory"]) : null;

            return new LazyLoadingMetadataFactory($loader, $cache);
        };
    }
}
