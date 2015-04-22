<?php

namespace Singo;

use Silex\Application as SilexApplication;
use Silex\Provider\CacheServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Singo\Contracts\Module\ModuleInterface;
use Singo\Provider\AnnotationRouting;
use Singo\Provider\CommandBus;
use Singo\Provider\Config;
use Singo\Provider\ContainerAwareEventDispatcher;
use Singo\Provider\Firewall;
use Singo\Provider\Logger;
use Singo\Provider\Mailer;
use Singo\Provider\Orm;
use Singo\Provider\Validator;
use Stack\Builder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application
 * @package Singo
 */
class Application extends SilexApplication
{
    /**
     * @var Application
     */
    public static $container;

    /**
     * @var Builder
     */
    public $builder;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        if (! defined("APP_PATH")) {
            define("APP_PATH", $this["app.path"]);
        }

        if (! defined("PUBLIC_PATH")) {
            define("PUBLIC_PATH", $this["app.public.path"]);
        }

        $this->builder = new Builder();
    }

    /**
     * @param Request $request
     */
    public function run(Request $request = null)
    {
        /**
         * resolve stack middleware for our apps
         */
        $app = $this->builder->resolve($this);

        /**
         * override current apps with stacked http kernel
         */
        if ($request === null) {
            $request =  Request::createFromGlobals();
        }

        $response = $app->handle($request);
        $response->send();
        $this->terminate($request, $response);
    }

    /**
     * initialize our application
     */
    public function init()
    {
        /**
         * container aware event dispatcher
         */
        $this->register(new ContainerAwareEventDispatcher());

        /**
         * cache
         */
        $this->register(new CacheServiceProvider());

        /**
         * configuration
         */
        $this->register(new Config());

        /**
         * logger
         */
        $this->register(new Logger());

        /**
         * doctrine orm
         */
        $this->register(new Orm());

        /**
         * validator
         */
        $this->register(new Validator());

        /**
         * mailer
         */
        $this->register(new Mailer());

        /**
         * command bus
         */
        $this->register(new CommandBus());

        /**
         * security
         */
        $this->register(new Firewall());

        /**
         * controller as a service
         */
        $this->register(
            new ServiceControllerServiceProvider(),
            [
                "tactician.inflector" => "class_name",
                "tactician.middlewares" =>
                    [
                        $this["command.bus.logger.middleware"],
                        $this["command.bus.validation.middleware"]
                    ]
            ]
        );

        /**
         * boot module if module feature enabled in configuration
         */
        if ($this->offsetExists("use.module") && $this["use.module"] === true) {
            $this->bootModule();
        }

        /**
         * Save container in static variable
         */
        self::$container = $this;
    }

    /**
     * register command for our application
     * @param array $commands
     * @param callable $handler
     */
    public function registerCommands(array $commands, callable $handler)
    {
        foreach ($commands as $command) {
            $handler_id = "app.handler." . join('', array_slice(explode("\\", $command), -1));
            $this[$handler_id] = $handler;
        }
    }

    /**
     * register our event subscriber
     * @param string $class
     * @param callable $callback
     */
    public function registerSubscriber($class, callable $callback)
    {
        $service_id = "event." . strtolower(str_replace("\\", ".", $class));

        $this[$service_id] = $callback;

        $this["dispatcher"]->addSubscriberService($service_id, $class);
    }

    /**
     * register stack middleware
     * @param string $class
     */
    public function registerStackMiddleware($class)
    {
        if (func_num_args() === 0) {
            throw new \InvalidArgumentException("Missing argument(s) when calling registerStackMiddlerware");
        }

        if (! class_exists($class)) {
            throw new \InvalidArgumentException("{$class} not found!");
        }

        call_user_func_array([$this->builder, "push"], func_get_args());
    }

    /**
     * boot module
     */
    protected function bootModule()
    {
        $base_namespace = $this["config"]->get("modules/base_namespace");
        $modules = $this["config"]->get("modules/modules");

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
             * register service provider
             */
            $this->register($module_object);

            /**
             * register command and handler
             */
            $module_object->command($this);

            /**
             * replace controller with full namespace
             */
            $routes = $module[key($module)]["controllers"];
            array_walk_recursive($routes, function (&$item) use ($module_namespace) {
                $item = $module_namespace . "\\Controllers\\" . $item;
            });

            $controllers = array_merge($controllers, $routes);
        }

        /**
         * register annotation controller
         */
        $this->register(
            new AnnotationRouting(),
            [
                "annot.cache" => (isset($this["cache.factory"])) ? $this["cache.factory"] : null,
                "annot.controllers" => $controllers
            ]
        );
    }
}
