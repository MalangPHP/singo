<?php


namespace Singo\Tests\Handlers;

use Silex\Component\Security\Core\Encoder\JWTEncoder;
use Singo\Tests\Commands\LoginCommand;

/**
 * Class LoginHandler
 * @package Singo\Tests\Handlers
 */
class LoginHandler
{
    /**
     * @var JWTEncoder
     */
    private $jwt_encoder;

    /**
     * @param JWTEncoder $jwt_encoder
     */
    public function __construct(JWTEncoder $jwt_encoder)
    {
        $this->jwt_encoder = $jwt_encoder;
    }

    /**
     * @param $command
     * @return string
     */
    public function handleLoginCommand($command)
    {
        if (! $command instanceof LoginCommand) {
            throw new \DomainException("Command must be instance of " . LoginCommand::class);
        }

        if ($command->getUsername() == "admin" && $command->getPassword() == "singo") {
            return $this->signToken($command->getUsername());
        }

        throw new \InvalidArgumentException("invalid username and/org password");
    }

    /**
     * @param $username
     * @return string
     */
    private function signToken($username)
    {
        return $this->jwt_encoder->encode(["name" => $username]);
    }
}
