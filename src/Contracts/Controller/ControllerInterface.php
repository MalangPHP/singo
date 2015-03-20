<?php


namespace Singo\Contracts\Controller;

use League\Fractal\Manager;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Interface ControllerInterface
 * @package Singo\Contracts\Controller
 */
interface ControllerInterface
{
    /**
     * @param RequestStack $request
     * @param Manager $fractal
     * @param CommandBus $bus
     */
    public function __construct(RequestStack $request, Manager $fractal, CommandBus $bus);
}

// EOF
