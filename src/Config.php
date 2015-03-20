<?php


namespace Singo;

use Pimple\Container;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 * @package Singo
 */
class Config
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $data;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->data = Yaml::parse(file_get_contents(__DIR__ . "/Resources/Config/config.yml"));
    }

    /**
     * @param string $path
     * @param null|mixed $default
     * @return mixed
     */
    public function get($path, $default = null)
    {
        $path = explode("/", $path);

        if (empty($path[0]) || !isset($this->data[$path[0]])) {
            return false;
        }

        $part = &$this->data;
        $value = null;

        foreach ($path as $key) {
            if (!isset($part[$key])) {
                $value = null;
                break;
            }

            $value = $part[$key];
            $part = &$part[$key];
        }

        if ($value !== null) {
            return $value;
        }

        return $default;
    }
}

// EOF
