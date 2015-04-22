<?php


namespace Singo\Tests\Modules\Main;

use Pimple\Container;
use Silex\Application;
use Singo\Application as Singo;
use Singo\Contracts\Module\ModuleInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Module implements ModuleInterface
{
    public function register(Container $app)
    {

    }

    public function boot(Application $app)
    {

    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {

    }

    public function command(Singo $app)
    {

    }
}

