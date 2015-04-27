<?php

namespace Singo\Contracts\Singo;

use Singo\Application;

/**
 * Interface PimpleAwareInterface
 * @package Singo\Contracts\Pimple
 */
interface ContainerAwareInterface
{
    /**
     * @param Application $app
     */
    public function setContainer(Application $app);

    /**
     * @return Application
     */
    public function getContainer();
}
