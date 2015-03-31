<?php


namespace Singo\Tests\Listeners;

use Singo\Tests\Event\Manager\OutputTestEvent;
use Singo\Tests\Event\TestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TestListener
 * @package Singo\Tests\Listeners
 */
class TestListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TestEvent::TEST_EVENT => ["onTestEvent", 0]
        ];
    }

    /**
     * @param OutputTestEvent $event
     * @return string
     */
    public function onTestEvent(OutputTestEvent $event)
    {
        return $event->getValue();
    }
}

// EOF
