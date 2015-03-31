<?php


namespace Singo\Tests;

use Pimple\Container;
use Singo\Application;
use Singo\Tests\Controllers\TestController;
use Singo\Tests\Event\TestEvent;
use Singo\Tests\Handlers\GeneralHandler;
use Singo\Tests\Provider\UserProvider;
use Singo\Tests\Subscribers\TestSubscriber;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApplicationTest
 * @package Singo\Tests
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $app;

    /**
     * initialize test suite
     */
    public function setUp()
    {
        $this->app = new Application(
            [
                "app.path" => __DIR__,
                "app.public.path" => __DIR__,
                "config.path" => __DIR__ . "/Config/config.yml"
            ]
        );

        $this->app["users"] = function () {
            return new UserProvider();
        };

        $this->app->init();
    }

    /**
     * Test route that didn't require authentication
     * return void
     */
    public function testPublicRoute()
    {
        $this->app["test.controller"] = function(Container $container) {
            return new TestController(
                $container["request_stack"],
                $container["fractal.manager"],
                $container["command.bus"]
            );
        };

        $this->app->get("/test", "test.controller:indexAction");

        $request = Request::create("/test");

        $this->assertEquals("ok", $this->app->handle($request)->getContent());
    }

    /**
     * Test route that require authentication
     * return void
     */
    public function testRestrictedRoute()
    {
        $this->app["test.controller"] = function(Container $container) {
            return new TestController(
                $container["request_stack"],
                $container["fractal.manager"],
                $container["command.bus"]
            );
        };

        $this->app->get("/vip", "test.controller:indexAction");

        $request = Request::create("/vip");

        $this->assertEquals("401", $this->app->handle($request)->getStatusCode());
    }

    /**
     * Test valid command
     * return void
     */
    public function testCommandBus()
    {
        $this->app["test.controller"] = function(Container $container) {
            return new TestController(
                $container["request_stack"],
                $container["fractal.manager"],
                $container["command.bus"]
            );
        };

        $this->app->registerCommands(
            [
                Commands\TestCommand::class
            ],
            function () {
                return new GeneralHandler();
            }
        );

        $this->app->get("/command", "test.controller:commandAction");

        $request = Request::create("/command");

        $this->assertEquals("jowy", $this->app->handle($request)->getContent());
    }

    public function testCommandValidation()
    {
        $this->app["test.controller"] = function(Container $container) {
            return new TestController(
                $container["request_stack"],
                $container["fractal.manager"],
                $container["command.bus"]
            );
        };

        $this->app->registerCommands(
            [
                Commands\TestCommand::class
            ],
            function () {
                return new GeneralHandler();
            }
        );

        $this->app->get("/validate", "test.controller:validateAction");

        $request = Request::create("/validate");

        $this->assertContains("blank", $this->app->handle($request)->getContent());
    }

    public function testEventDispatcher()
    {
        $this->app->registerSubscriber(TestSubscriber::class, function() {
            return new TestSubscriber();
        });

        $event = $this->app["dispatcher"]->dispatch(TestEvent::TEST_EVENT, new TestEvent());

        $this->assertEquals($event->getNick(), "jowy");
    }
}
