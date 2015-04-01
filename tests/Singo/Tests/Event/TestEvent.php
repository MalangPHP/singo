<?php


namespace Singo\Tests\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class TestEvent
 * @package Singo\Tests\Event
 */
final class TestEvent extends Event
{
    const TEST_EVENT = "test.event";

    /**
     * @var string
     */
    protected $nick;

    /**
     * @return string
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * @param string $nick
     */
    public function setNick($nick)
    {
        $this->nick = $nick;
    }
}
