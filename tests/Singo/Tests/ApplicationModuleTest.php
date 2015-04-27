<?php


namespace Singo\Tests;

use Singo\Application;
use Singo\Tests\Provider\UserProvider;
use Symfony\Component\HttpFoundation\Request;

class ApplicationModuleTest extends \PHPUnit_Framework_TestCase
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
                "config.path" => __DIR__ . "/Config/config.yml",
                "config.cache.lifetime" => 300,
                "cache.driver" => "array",
                "use.module" => true,
                "cache.options" => [
                    "namespace" => "singo"
                ]
            ]
        );

        $this->app->init($this->app);

        $this->app["users"] = function () {
            return new UserProvider();
        };
    }

    public function tearDown()
    {
        unset($this->app);
    }

    public function testModule()
    {
        $req = Request::create("/home");

        $response = $this->app->handle($req);

        $this->assertEquals("hello world", $response->getContent());
    }
}

