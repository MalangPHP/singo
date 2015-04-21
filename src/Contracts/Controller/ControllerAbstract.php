<?php

namespace Singo\Contracts\Controller;

use League\Tactician\CommandBus;

/**
 * Class ControllerAbstract
 * @package Singo\Contracts\Controller
 */
abstract class ControllerAbstract implements ControllerInterface
{
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
