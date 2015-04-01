<?php


namespace Singo\Contracts\Controller;

use League\Fractal\Manager;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ControllerAbstract
 * @package Singo\Contracts\Controller
 */
abstract class ControllerAbstract implements ControllerInterface
{
    /**
     * @var RequestStack
     */
    protected $request;

    /**
     * @var CommandBus
     */
    protected $bus;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * {@inheritdoc}
     */
    public function __construct(RequestStack $request, Manager $fractal, CommandBus $bus)
    {
        $this->request = $request;
        $this->fractal = $fractal;
        $this->bus = $bus;
    }
}
