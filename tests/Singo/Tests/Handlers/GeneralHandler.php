<?php


namespace Singo\Tests\Handlers;


/**
 * Class GeneralHandler
 * @package Singo\Tests\Handler
 */
class GeneralHandler
{
    /**
     * @param $command
     * @return mixed
     */
    public function handleTestCommand($command)
    {
        return $command->name;
    }
}

// EOF
