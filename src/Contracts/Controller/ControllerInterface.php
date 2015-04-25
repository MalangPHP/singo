<?php

namespace Singo\Contracts\Controller;

use League\Tactician\CommandBus;
use Singo\Contracts\CommandBus\CommandBusAwareInterface;

/**
 * Interface ControllerInterface
 * @package Singo\Contracts\Controller
 */
interface ControllerInterface extends CommandBusAwareInterface
{
    /**
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus);
}
