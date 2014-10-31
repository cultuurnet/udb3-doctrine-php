<?php

namespace spec\CultuurNet\UDB3\Doctrine;

use CultuurNet\UDB3\Doctrine\EventServiceCache;
use CultuurNet\UDB3\EventServiceInterface;
use Doctrine\Common\Cache\Cache;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin EventServiceCache
 */
class EventServiceCacheSpec extends ObjectBehavior
{
    function let(
        EventServiceInterface $eventService,
        Cache $cache
    )
    {
        $this->beConstructedWith($eventService, $cache);
    }

    function it_is_initializable_with_an_EventServiceInterface_and_a_Doctrine_Cache() {
        $this->shouldHaveType('CultuurNet\UDB3\Doctrine\EventServiceCache');
    }

    function it_implements_EventServiceInterface()
    {
        $this->shouldHaveType('CultuurNet\UDB3\EventServiceInterface');
    }

    function it_first_tries_to_get_event_data_from_the_cache(
        EventServiceInterface $eventService,
        Cache $cache
    ) {
        $this->beConstructedWith(
            $eventService,
            $cache
        );

        $eventId = 'foo';

        $cache
            ->fetch($eventId)
            ->shouldBeCalled();

        $this->getEvent($eventId);
    }

    function it_prefers_the_value_from_cache(
        EventServiceInterface $eventService,
        Cache $cache
    ) {
        $this->beConstructedWith($eventService, $cache);

        $eventId = 'foo';

        $cache
            ->fetch($eventId)
            ->willReturn('whatever-from-cache');

        $this->getEvent($eventId)->shouldReturn('whatever-from-cache');
    }

    function it_calls_the_decorated_EventServiceInterface_if_there_is_no_cache_and_fills_the_cache(
        EventServiceInterface $eventService,
        Cache $cache
    ) {
        $this->beConstructedWith($eventService, $cache);

        $eventId = 'foo';
        $eventValueFromEventService = 'whatever-from-event-service';

        $cache
            ->fetch($eventId)
            ->willReturn(false);

        $eventService
            ->getEvent($eventId)
            ->willReturn($eventValueFromEventService);

        $cache
            ->save($eventId, $eventValueFromEventService, Argument::type('integer'))
            ->willReturn(true);

        $this
            ->getEvent($eventId)
            ->shouldReturn($eventValueFromEventService);
    }

    function its_default_cache_time_is_3600_seconds (
        EventServiceInterface $eventService,
        Cache $cache
    )
    {
        $this->beConstructedWith($eventService, $cache);

        $eventId = 'foo';
        $cache
            ->fetch($eventId)
            ->willReturn(false);
        $cache
            ->save($eventId, Argument::any(), 3600)
            ->shouldBeCalled();

        $this->getEvent($eventId);
    }

    function its_cache_lifetime_is_adjustable(
        EventServiceInterface $eventService,
        Cache $cache
    ) {
        $this->beConstructedWith($eventService, $cache);

        $customLifetime = 300;
        $this->setLifetime($customLifetime);

        $eventId = 'foo';

        $cache
            ->fetch($eventId)
            ->willReturn(false);
        $cache
            ->save($eventId, Argument::any(), $customLifetime)
            ->shouldBeCalled();

        $this->getEvent($eventId);
    }
}
