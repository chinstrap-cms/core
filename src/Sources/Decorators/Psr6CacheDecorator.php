<?php

declare(strict_types=1);

namespace Chinstrap\Core\Sources\Decorators;

use Chinstrap\Core\Contracts\Objects\Document;
use Chinstrap\Core\Contracts\Sources\Source;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use PublishingKit\Utilities\Contracts\Collectable;

final class Psr6CacheDecorator implements Source
{
    private CacheItemPoolInterface $cache;

    private Source $source;

    public function __construct(CacheItemPoolInterface $cache, Source $source)
    {
        $this->cache = $cache;
        $this->source = $source;
    }

    public function all(): Collectable
    {
        return $this->source->all();
    }

    public function find(string $name): ?Document
    {
        $item = $this->cache->getItem('Documents/find/' . $name);
        if ($item->isHit()) {
            $result = $item->get();
            if (!$result instanceof Document) {
                throw new Exception('Non-document returned from cache');
            }
            return $result;
        }
        $result = $this->source->find($name);
        $item->set($result);
        $this->cache->save($item);
        return $result;
    }
}
