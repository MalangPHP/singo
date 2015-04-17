<?php


namespace Singo\Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Silex\Provider\CacheServiceProvider;
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
    }

    /**
     * test route that didn't require authentication
     */
    public function testPublicRoute()
    {
        $this->app->init();
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
        $this->app->init();
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
        $this->app->init();
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
        $this->app->init();
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
        $this->app->init();
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
        $this->app->init();
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
    public function testMultipleOrmInstance()
    {
        $this->app->init();
        $mysql_read = $this->app["orm.ems"]["mysql_read"];
        $mysql_write = $this->app["orm.ems"]["mysql_write"];

        $this->assertInstanceOf(EntityManager::class, $mysql_read);
        $this->assertInstanceOf(EntityManager::class, $mysql_write);
    }

    public function testMultipleOdmInstance()
    {
        $this->app->init();
        if (PHP_VERSION_ID < 70000
            && ! defined("HHVM_VERSION")
            && extension_loaded("mongo")
        ) {
            $mongo_read = $this->app["mongodbodm.dms"]["mongo_read"];
            $mongo_write = $this->app["mongodbodm.dms"]["mongo_write"];

            $this->assertInstanceOf(DocumentManager::class, $mongo_read);
            $this->assertInstanceOf(DocumentManager::class, $mongo_write);
        }
    }

    public function testCacheConfig()
    {
        $this->app->register(
            new CacheServiceProvider(),
            [
                "cache.driver"  => "array",
                "cache.options" => [
                    "namespace" => "singo"
                ],
                "config.cache.lifetime" => 300
            ]
        );

        $this->app->init();

        $config = $this->app["config"];
        $cached_config = $this->app["config"];

        $this->assertEquals($config, $cached_config);

        $log_name = $this->app["config"]->get("common/log/name");
        $cached_log_name = $this->app["config"]->get("common/log/name");

        $this->assertEquals($log_name, $cached_log_name);
    }
}
