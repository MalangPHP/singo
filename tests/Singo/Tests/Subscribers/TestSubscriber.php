<?php


namespace Singo\Tests\Subscribers;

use Singo\Tests\Event\TestEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TestSubscriber
 * @package Singo\Tests\Subscribers
 */
class TestSubscriber implements EventSubscriberInterface
{
    /**
     * @param Event $event
     */
    public function onTestEvent(Event $event)
    {
        if (!$event instanceof TestEvent) {
            throw new \RuntimeException("invalid event");
        }

        $event->setNick("jowy");
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            "test.event" => [
                [
                    "onTestEvent",
                    0
                ]
            ]
        ];
    }
}
