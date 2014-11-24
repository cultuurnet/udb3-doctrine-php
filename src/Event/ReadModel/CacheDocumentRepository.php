<?php
/**
 * @file
 */

namespace CultuurNet\UDB3\Doctrine\Event\ReadModel;

use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;

use CultuurNet\UDB3\Event\ReadModel\JsonDocument;
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

    if (false === $value) {
      return NULL;
    }

    return new JsonDocument($id, $value);
  }

  public function save(JsonDocument $document)
  {
    $this->cache->save($document->getId(), $document->getRawBody(), 0);
  }
}

