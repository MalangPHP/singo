<?php


namespace Singo\Tests\Modules\User;

use Singo\Contracts\Module\ModuleInterface;

class Module implements ModuleInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return "User Module";
    }
}
