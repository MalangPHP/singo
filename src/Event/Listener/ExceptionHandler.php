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
        if ($this->container["sable.config"]->get("common/debug")) {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof InvalidCommandException) {
            $message = explode("|", $exception->getMessage());

            $event->setResponse(new JsonResponse(
                [
                    "error" =>
                    [
                        $message[0] => $message[1]
                    ]
                ]
            ), Response::HTTP_BAD_REQUEST);
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

// EOF
