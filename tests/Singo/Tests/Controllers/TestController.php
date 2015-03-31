<?php


namespace Singo\Tests\Controllers;

use Singo\Contracts\Controller\ControllerAbstract;
use Singo\Tests\Commands\TestCommand;

/**
 * Class TestController
 * @package Singo\Tests\Controllers
 */
class TestController extends ControllerAbstract
{
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
    public function eventAction()
    {

    }
}
