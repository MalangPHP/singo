<?php

namespace Singo\Contracts\Controller;

use League\Tactician\CommandBus;

/**
 * Interface ControllerInterface
 * @package Singo\Contracts\Controller
 */
interface ControllerInterface
{
    /**
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus);
}
