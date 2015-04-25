<?php

namespace Singo\Contracts\Console;

use League\Tactician\CommandBus;
use Singo\Contracts\CommandBus\CommandBusAwareTrait;
use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractCommand
 * @package Singo\Contracts\Command
 */
abstract class AbstractCommand extends Command implements CommandInterface
{
    use CommandBusAwareTrait;

    /**
     * @param null $name
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus, $name = null)
    {
        parent::__construct($name);

        $this->bus = $bus;
    }
}
