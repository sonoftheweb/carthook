<?php

namespace App\Helpers\Guzzle;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class GuzzleCacheMiddleware
{
    /**
     * PSR-16 Cache interface implementation.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Duration to live in cache
     *
     * @var int Seconds
     */
    protected $ttl = 60;

    /**
     * Log the cache requests?
     *
     * @var bool
     */
    protected $track = false;

    /**
     * Cache the laravel cache driver instance.
     *
     * @param CacheInterface $cache
     * @param int $ttl
     * @param bool $log
     */
    public function __construct(CacheInterface $cache, int $ttl = 60, bool $log = false)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
        $this->track = $log;
    }

    /**
     * The middleware handler
     *
     * @param callable $handler
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use (&$handler) {
            # Create the cache key
            $key = $this->makeKey($options, $request->getUri());

            # Try to get from cache
            if ($key && $entry = $this->get($key)) {
                return $entry;
            }

            /** @var PromiseInterface $promise */
            $promise = $handler($request, $options);

            return $promise->then(
                function (ResponseInterface $response) use ($options, $key) {
                    if ($key && $ttl = $this->getTTL($options)) {
                        $this->save($key, $response, $ttl);
                    }

                    return $response;
                }
            );
        };
    }

    /**
     * Create the key which will reference the cache entry.
     *
     * @param array $options
     * @param UriInterface $uri
     * @return string
     */
    protected function makeKey(array $options, UriInterface $uri): string
    {
        # Does the specific request allow caching?
        $cache = $options['cache'] ?? true;

        # Either return the custom passed key or the request URL minus the protocol.
        return $cache
            ? ($options['cache_key'] ?? preg_replace('#(https?:)#', '', (string)$uri))
            : '';
    }

    /**
     * Get duration the data should stay in cache.
     *
     * @param array $options
     * @return int $seconds
     */
    protected function getTTL(array $options): int
    {
        $duration = $options['cache_ttl'] ?? $this->ttl;

        $duration = $duration !== -1 ? $duration : 631152000;

        return $duration;
    }

    /**
     * Cache the data.
     * @param string $key
     * @return FulfilledPromise|null
     * @throws InvalidArgumentException
     */
    protected function get(string $key): ?FulfilledPromise
    {
        $entry = $this->cache->get($key);

        if (is_null($entry)) {
            return $entry;
        }

        if ($this->track) {
            $this->log($key);
        }

        return new FulfilledPromise(
            new Response(200, [], $entry)
        );
    }

    /**
     * Persist the data.
     *
     * @param string $key
     * @param ResponseInterface|null $response
     * @param int $ttl
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function save(string $key, ?ResponseInterface $response, int $ttl): bool
    {
        if ($response && $response->getStatusCode() === 200) {
            $saved = $this->cache->set($key, (string)$response->getBody(), $ttl) ?? true;

            if ($response->getBody()->isSeekable()) {
                $response->getBody()->rewind();
            }

            return $saved;
        }

        return false;
    }

    /**
     * Track the cache request to a log file.
     *
     * @param string $cacheKey
     * @return mixed
     */
    protected function log(string $cacheKey)
    {
        $msg = "Retrieved from cache: $cacheKey";

        # Laravel support
        if (function_exists('logger')) {
            return logger($msg);
        }

        return error_log($msg);
    }
}
