<?php
/**
 * @file
 */

namespace CultuurNet\UDB3\Doctrine\Event\ReadModel;

use CultuurNet\UDB3\Event\ReadModel\DocumentGoneException;
use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;
use CultuurNet\UDB3\ReadModel\JsonDocument;
use Doctrine\Common\Cache\Cache;

class CacheDocumentRepository implements DocumentRepositoryInterface
{
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function get($id)
    {
        $value = $this->cache->fetch($id);

        if ('GONE' === $value) {
            throw new DocumentGoneException();
        }

        if (false === $value) {
            return null;
        }

        return new JsonDocument($id, $value);
    }

    public function save(JsonDocument $document)
    {
        $this->cache->save($document->getId(), $document->getRawBody(), 0);
    }

    public function remove($id)
    {
        $this->cache->save($id, 'GONE', 0);
    }
}

