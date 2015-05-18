<?php

namespace Singo\Event\Listener;

use Psr\Log\LoggerInterface;
use Silex\Component\Config\Driver\AbstractConfigDriver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ExceptionHandler
 * @package Singo\Event\Listener
 */
final class ExceptionHandler implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AbstractConfigDriver
     */
    private $config;

    /**
     * @param AbstractConfigDriver $config
     * @param LoggerInterface $logger
     */
    public function __construct(AbstractConfigDriver $config, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onSilexError(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->logger->error($exception->getMessage());

        if ($this->config->get("common/debug")) {
            return;
        }

        /**
         * dont show error message if exception are not from http exception
         */
        if (! $exception instanceof HttpExceptionInterface) {
            $event->setResponse(new JsonResponse(
                [
                    "error" => "Internal error"
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));
            return;
        }

        /**
         * show http exception error
         */
        $event->setResponse(new JsonResponse(
            [
                "error" => $exception->getMessage()
            ],
            $exception->getStatusCode()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => "onSilexError"
        ];
    }
}
