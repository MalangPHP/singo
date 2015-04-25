<?php

namespace Singo\Contracts\CommandBus;

use League\Tactician\CommandBus;

/**
 * Interface CommandBusAwareInterface
 * @package Singo\Contracts\CommandBus
 */
interface CommandBusAwareInterface
{
    /**
     * @param CommandBus $bus
     */
    public function setCommandBus(CommandBus $bus);

    /**
     * @return CommandBus
     */
    public function getCommandBus();
}
