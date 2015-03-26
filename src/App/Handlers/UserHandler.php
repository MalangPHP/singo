<?php


namespace Singo\App\Handlers;

use Silex\Component\Security\Core\Encoder\JWTEncoder;
use Singo\App\Commands\LoginCommand;
use Singo\Contracts\Bus\CommandInterface;
use Singo\Contracts\Bus\HandlerInterface;
use Singo\App\Handlers\Exceptions\InvalidCredentialsException;

/**
 * Class UserHandler
 * @package Singo\App\Handlers
 */
class UserHandler implements HandlerInterface
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
     * @param CommandInterface $command
     * @return string
     * @throws InvalidCredentialsException
     */
    public function handleLoginCommand(CommandInterface $command)
    {
        if (! $command instanceof LoginCommand) {
            throw new \DomainException("Command must be instance of " . LoginCommand::class);
        }

        if ($command->getUsername() == "admin" && $command->getPassword() == "singo") {
            return $this->signToken($command->getUsername());
        }

        throw new InvalidCredentialsException("Invalid username and/or password");
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

// EOF
