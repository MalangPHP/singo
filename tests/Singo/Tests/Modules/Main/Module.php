<?php


namespace Singo\Tests\Modules\Main;

use Singo\Contracts\Module\CliCommandProviderInterface;
use Singo\Contracts\Module\ModuleInterface;
use Singo\Application as Singo;
use Singo\Tests\Modules\Main\Cli\Commands\HelloWorldCommand;
use Symfony\Component\Console\Application as CLI;

class Module implements ModuleInterface, CliCommandProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function cli(CLI $app, Singo $container)
    {
        $app->add(new HelloWorldCommand($container["command.bus"]));
    }
}

