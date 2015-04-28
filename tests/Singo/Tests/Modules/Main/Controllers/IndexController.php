<?php


namespace Singo\Tests\Modules\Main\Controllers;

use League\Tactician\CommandBus;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Silex\PimpleAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/")
 */
class IndexController
{
    /**
     * @var CommandBus
     */
    protected $bus;

    /**
     * @var PimpleAwareEventDispatcher
     */
    protected $dispatcher;

    /**
     * @param CommandBus $bus
     * @param PimpleAwareEventDispatcher $dispatcher
     */
    public function __construct(CommandBus $bus, PimpleAwareEventDispatcher $dispatcher)
    {
        $this->bus = $bus;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="home")
     * )
     */
    public function indexAction()
    {
        return new Response("hello world");
    }
}

