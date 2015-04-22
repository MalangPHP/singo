<?php

namespace Singo\Event\Listener;

use Pimple\Container;
use Singo\Bus\Exception\InvalidCommandException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ExceptionHandler
 * @package Singo\Event\Listener
 */
final class ExceptionHandler implements EventSubscriberInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onSilexError(GetResponseForExceptionEvent $event)
    {
        if ($this->container["config"]->get("common/debug")) {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof InvalidCommandException
            || $exception instanceof \InvalidArgumentException) {
            $event->setResponse(new JsonResponse(
                [
                    "error" => $exception->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            ));
        }

        if ($exception instanceof \Exception) {
            $event->setResponse(new JsonResponse(
                [
                    "error" => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));
        }
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
