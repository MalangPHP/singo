<?php


namespace Singo\Bus\Middleware;

use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;

/**
 * Class CommandLoggerMiddleware
 * @package Singo\Bus\Middleware
 */
class CommandLoggerMiddleware implements Middleware
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        $command_name = get_class($command);

        $this->logger->info("executing {$command_name}");

        $return_value = $next($command);

        $this->logger->info("{$command_name} executed with no error");

        return $return_value;
    }
}
