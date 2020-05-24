<?php

namespace App\Helpers\Guzzle;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Psr\SimpleCache\CacheInterface;

class Client extends GuzzleClient
{
    /**
     * Create a middleware stack and instantiate Guzzle.
     *
     * {@inheritdoc}
     * @param array $config
     */
    public function __construct(CacheInterface $cache, array $config = [])
    {
        if (empty($config['handler'])) {
            $config['handler'] = HandlerStack::create();
        }

        $ttl = $config['cache_ttl'] ?? 60;
        $log = $config['cache_log'] ?? false;

        $config['handler']->push(new GuzzleCacheMiddleware($cache, $ttl, $log));

        unset($config['cache'], $config['cache_ttl'], $config['cache_log'], $config['cache_key']);

        parent::__construct($config);
    }
}
