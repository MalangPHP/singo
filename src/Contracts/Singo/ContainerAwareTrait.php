<?php

namespace Singo\Contracts\Singo;

use Singo\Application;

/**
 * Class ContainerAwareTrait
 * @package Singo\Contracts\Singo
 */
trait ContainerAwareTrait
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function setContainer(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Application
     */
    public function getContainer()
    {
        return $this->app;
    }
}
