<?php

namespace Neoflow\Framework\Persistence\Caching;

interface CacheInterface
{

    /**
     * Fetch cache value.
     *
     * @param string $key
     *
     * @return mixed
     */
    abstract public function fetch($key);

    /**
     * Store cache value.
     *
     * @param string $key
     * @param mixed  $data
     * @param int    $ttl
     * @param array  $tags
     *
     * @return bool
     */
    abstract public function store($key, $data, $ttl, array $tags);

    /**
     * Delete cache value.
     *
     * @param string $key
     *
     * @return bool
     */
    abstract public function delete($key);

    /**
     * Check wether cache value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    abstract public function exists($key);

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    abstract public function clear();
}
