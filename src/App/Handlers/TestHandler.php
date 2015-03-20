<?php


namespace Singo\App\Handlers;

use Singo\App\Commands\TestCommand;
use Singo\Contracts\Bus\HandlerInterface;

/**
 * Class TestHandler
 * @package Singo\Handlers
 */
class TestHandler implements HandlerInterface
{
    /**
     * @param TestCommand $command
     * @return array
     */
    public function handleTestCommand(TestCommand $command)
    {
        return [
            "name"  => $command->name,
            "email" => $command->email,
            "location" => $command->location
        ];
    }
}

// EOF
