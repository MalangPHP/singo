<?php


namespace Singo\Tests\Commands;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TestCommand
 * @package Singo\Tests\Commands
 */
class TestCommand
{
    /**
     * @Assert\Length(min = 3)
     * @Assert\NotBlank
     * @var string
     */
    public $name;
}
