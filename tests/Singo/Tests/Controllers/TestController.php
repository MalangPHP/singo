<?php


namespace Singo\Tests\Controllers;

use League\Tactician\CommandBus;
use Singo\Tests\Commands\LoginCommand;
use Singo\Tests\Commands\TestCommand;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class TestController
 * @package Singo\Tests\Controllers
 */
class TestController
{
    /**
     * @var CommandBus
     */
    protected $bus;

    /**
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @return string
     */
    public function indexAction()
    {
        return "ok";
    }

    /**
     * @return mixed
     */
    public function commandAction()
    {
        $command = new TestCommand();
        $command->name = "jowy";

        return $this->bus->handle($command);
    }

    /**
     * @return mixed
     */
    public function validateAction()
    {
        $command = new TestCommand();

        return $this->bus->handle($command);
    }

    /**
     * return mixed
     */
    public function loginAction()
    {
        $command = new LoginCommand();
        $command->setUsername("admin");
        $command->setPassword("singo");

        $token = $this->bus->handle($command);

        return new JsonResponse(
            [
                "data" => [
                    "token" => $token
                ]
            ]
        );
    }
}
