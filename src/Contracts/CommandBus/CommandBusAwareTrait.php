<?php

namespace Singo\Contracts\CommandBus;

use League\Tactician\CommandBus;

/**
 * Class CommandBusAwareTrait
 * @package Singo\Contracts\CommandBus
 */
trait CommandBusAwareTrait
{
    /**
     * @var CommandBus
     */
    protected $bus;

    /**
     * @param CommandBus $bus
     */
    public function setCommandBus(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @return CommandBus
     */
    public function getCommandBus()
    {
        return $this->bus;
    }
}
