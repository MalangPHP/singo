<?php

namespace Singo;

use Pimple\ServiceProviderInterface;
use Singo\Contracts\Module\CliCommandProviderInterface;
use Singo\Contracts\Module\CommandHandlerProviderInterface;
use Singo\Contracts\Module\ModuleInterface;
use Singo\Provider\AnnotationRouting;
use Symfony\Component\Console\Application as CLI;

/**
 * Class ModuleBooter
 * @package Singo
 */
trait ModuleBooter
{
    /**
     * boot module
     */
    protected function bootModule()
    {
        $base_namespace = self::$container["config"]->get("modules/base_namespace");
        $modules = self::$container["config"]->get("modules/modules");

        $controllers = [];

        foreach ($modules as $module)
        {
            $module_namespace = $base_namespace . "\\" . key($module);
            $module_class = $module_namespace . "\\Module";
            $module_object = new $module_class();

            if (! $module_object instanceof ModuleInterface) {
                throw new \InvalidArgumentException(
                    "Module {$module_class} must be instance of ServiceProviderInterface"
                );
            }

            /**
             * register service provider and command handler
             */
            $this
                ->bootModuleServiceProvider($module_object)
                ->bootModuleCommand($module_object)
                ->bootModuleCli($module_object);

            /**
             * replace controller with full namespace
             */
            if (isset($module[key($module)]["controllers"])) {
                $routes = $module[key($module)]["controllers"];
                array_walk_recursive($routes, function (&$item) use ($module_namespace) {
                    $item = $module_namespace . "\\Controllers\\" . $item;
                });

                $controllers = array_merge($controllers, $routes);
            }
        }

        /**
         * register annotation controller
         */
        self::$container->register(
            new AnnotationRouting(),
            [
                "annot.cache" => (isset(self::$container["cache.factory"])) ? self::$container["cache.factory"] : null,
                "annot.controllers" => $controllers
            ]
        );
    }

    /**
     * @param ModuleInterface $module
     * @return $this
     */
    protected function bootModuleServiceProvider(ModuleInterface $module)
    {
        if ($module instanceof ServiceProviderInterface) {
            self::$container->register($module);
        }

        return $this;
    }

    /**
     * @param ModuleInterface $module
     * @return $this
     */
    protected function bootModuleCommand(ModuleInterface $module)
    {
        if ($module instanceof CommandHandlerProviderInterface) {
            $module->command(self::$container);
        }

        return $this;
    }

    /**
     * @param ModuleInterface $module
     * @return $this
     */
    protected function bootModuleCli(ModuleInterface $module)
    {
        if ($module instanceof CliCommandProviderInterface && $this instanceof CLI) {
            $module->cli($this, self::$container);
        }

        return $this;
    }
}
