<?php


namespace Singo\Tests;

use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Singo\Application;
use Singo\Tests\Controllers\TestController;
use Singo\Tests\Event\TestEvent;
use Singo\Tests\Handlers\GeneralHandler;
use Singo\Tests\Handlers\LoginHandler;
use Singo\Tests\Provider\UserProvider;
use Singo\Tests\Subscribers\TestSubscriber;
use Singo\Tests\Commands\LoginCommand;
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
     * test route that didn't require authentication
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
     * test route that require authentication
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
     * test valid command
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

    /**
     * test command validation middleware
     */
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

    /**
     * test event dispatcher
     */
    public function testEventDispatcher()
    {
        $this->app->registerSubscriber(TestSubscriber::class, function() {
            return new TestSubscriber();
        });

        $event = $this->app["dispatcher"]->dispatch(TestEvent::TEST_EVENT, new TestEvent());

        $this->assertEquals($event->getNick(), "jowy");
    }

    /**
     * test jwt authentication
     */
    public function testAuthenticate()
    {
        $this->app["test.controller"] = function(Container $container) {
            return new TestController(
                $container["request_stack"],
                $container["fractal.manager"],
                $container["command.bus"]
            );
        };

        $jwt_encoder = $this->app["security.jwt.encoder"];

        $this->app->registerCommands(
            [
                LoginCommand::class
            ],
            function () use ($jwt_encoder) {
                return new LoginHandler($jwt_encoder);
            }
        );

        $this->app->get("/login", "test.controller:loginAction");
        $this->app->get("/vip", "test.controller:indexAction");

        $request = Request::create("/login");

        $token = $this->app->handle($request)->getContent();

        // Ensure return token
        $this->assertContains("data", $token);

        $token = json_decode($token);

        // Create request to restricted area
        $request =  Request::create("/vip");

        // Fail if no auth header
        $this->assertEquals("401", $this->app->handle($request)->getStatusCode());

        $request->headers->add(
            [
                "AUTH-HEADER-TOKEN" => $token->data->token
            ]
        );

        $response = $this->app->handle($request)->getContent();

        // return ok if auth header if present and valid
        $this->assertEquals("ok", $response);
    }

    /**
     * test multiple entity manager instance
     */
    public function testMultipleDatabaseInstance()
    {
        $mysql_read = $this->app["orm.ems"]["mysql_read"];
        $mysql_write = $this->app["orm.ems"]["mysql_write"];

        $this->assertInstanceOf(EntityManager::class, $mysql_read);
        $this->assertInstanceOf(EntityManager::class, $mysql_write);
    }
}
