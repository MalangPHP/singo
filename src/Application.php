<?php

namespace Singo;

use Silex\Application as SilexApplication;
use Stack\Builder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application
 * @package Singo
 */
class Application extends SilexApplication
{
    use ServiceInitializator;
    use ModuleBooter;

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

        if (! class_exists($class) && ! is_callable($class)) {
            throw new \InvalidArgumentException("{$class} not found or not callable");
        }

        call_user_func_array([$this->builder, "push"], func_get_args());
    }
}
