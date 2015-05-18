<?php


namespace Singo\Tests;

use Singo\Application;
use Singo\CliApplication;
use Symfony\Component\Console\Tester\CommandTester;

class CliApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $container;

    /**
     * @var CliApplication
     */
    private $app;

    public function setUp()
    {
        $this->container = new Application(
            [
                "app.path" => __DIR__,
                "app.public.path" => __DIR__,
                "config.path" => __DIR__ . "/Config/config.yml",
                "config.cache.lifetime" => 300,
                "cache.driver" => "array",
                "use.module" => true,
                "cli.mode" => true,
                "cache.options" => [
                    "namespace" => "singo"
                ]
            ]
        );

        $this->app = new CliApplication($this->container);
        $this->app->setDispatcher($this->container["dispatcher"]);
    }

    public function tearDown()
    {
        unset($this->app);
    }

    public function testHelloWorldCommand()
    {
        $command = $this->app->find("hello:world");
        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertRegExp("/hello world/", $tester->getDisplay());
    }
}

