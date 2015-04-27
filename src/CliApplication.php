<?php

namespace Singo;

use Singo\Contracts\Singo\ContainerAwareInterface;
use Singo\Contracts\Singo\ContainerAwareTrait;
use Singo\Application as Singo;
use Symfony\Component\Console\Application as CLI;

/**
 * Class CliApplication
 * @package Singo
 */
class CliApplication extends CLI implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceInitializator;
    use ModuleBooter;

    /**
     * @param Singo $app
     * @param string $name
     * @param string $version
     */
    public function __construct(Singo $app, $name = "UNKNOWN", $version = "UNKNOWN")
    {
        parent::__construct($name, $version);

        $this->init($app);
    }
}
