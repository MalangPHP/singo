<?php

namespace Singo\Contracts\Controller;

use League\Tactician\CommandBus;
use Singo\Contracts\CommandBus\CommandBusAwareTrait;

/**
 * Class ControllerAbstract
 * @package Singo\Contracts\Controller
 */
abstract class ControllerAbstract implements ControllerInterface
{
    use CommandBusAwareTrait;

    /**
     * @var CommandBus
     */
    protected $bus;


    /**
     * {@inheritdoc}
     */
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }
}
