<?php


namespace Singo\App\Commands;

use Singo\Contracts\Bus\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TestCommand
 * @package Sable\Commands
 */
class TestCommand implements CommandInterface
{
    /**
     * @Assert\Length(min = 3)
     * @Assert\NotBlank
     * @var string
     */
    public $name;

    /**
     * @Assert\Email
     * @Assert\NotBlank
     * @var string
     */
    public $email;

    /**
     * @Assert\Length(min = 2)
     * @Assert\NotBlank
     * @var string
     */
    public $location;
}

// EOF
