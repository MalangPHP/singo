<?php

namespace Singo\Event\Listener;

use Psr\Log\LoggerInterface;
use Silex\Component\Config\Driver\AbstractConfigDriver;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CliExceptionHandler implements EventSubscriberInterface
{
    /**
     * @var AbstractConfigDriver
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param AbstractConfigDriver $config
     * @param LoggerInterface $logger
     */
    public function __construct(AbstractConfigDriver $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param ConsoleExceptionEvent $event
     */
    public function onConsoleError(ConsoleExceptionEvent $event)
    {
        $exception = $event->getException();
        $command = $event->getCommand();
        $output = $event->getOutput();

        $this->logger->error(sprintf("error while executing %s -> %s", $command->getName(), $exception->getMessage()));

        if ($this->config->get("common/debug")) {
            return;
        }

        $output->writeln(sprintf("error while executing %s -> %s", $command->getName(), $exception->getMessage()));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::EXCEPTION => "onConsoleError"
        ];
    }
}
