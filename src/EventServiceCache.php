<?php
/**
 * @file
 */

namespace CultuurNet\UDB3\Doctrine;


use CultuurNet\UDB3\EventServiceDecoratorBase;
use CultuurNet\UDB3\Event\EventServiceInterface;
use Doctrine\Common\Cache\Cache;

class EventServiceCache extends EventServiceDecoratorBase
{
    /**
     * @var Cache
     */
    protected $cacheProvider;

    /**
     * Cache lifetime, 1 hour by default.
     *
     * @var int
     */
    protected $lifetime = 3600;

    public function __construct(
        EventServiceInterface $eventService,
        Cache $cacheProvider
    ) {
        parent::__construct($eventService);

        $this->cacheProvider = $cacheProvider;
    }

    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    public function getEvent($id)
    {
        $data = $this->cacheProvider->fetch($id);
        if (false === $data) {
            $data = parent::getEvent($id);
            $this->cacheProvider->save($id, $data, $this->lifetime);
        }

        return $data;
    }


}
