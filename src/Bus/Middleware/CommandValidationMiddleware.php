<?php


namespace Singo\Bus\Middleware;

use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Singo\Bus\Exception\InvalidCommandException;
use Symfony\Component\Validator\Validator;

/**
 * Class CommandValidationMiddleware
 * @package Singo\Bus\Middleware
 */
class CommandValidationMiddleware implements Middleware
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Validator\ValidatorInterface $validator
     * @param LoggerInterface $logger
     */
    public function __construct(Validator\ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        $command_name = get_class($command);

        $this->logger->info("validating {$command_name}");

        $violation = $this->validator->validate($command);
        if ($violation->count() > 0) {
            $message = str_replace(
                "This value",
                ucfirst($violation->get(0)->getPropertyPath()),
                $violation->get(0)->getMessage()
            );
            $this->logger->error("{$command_name} : {$message}");
            throw new InvalidCommandException($message);
        }

        $this->logger->info("{$command_name} has passed validation");
        return $next($command);
    }
}
