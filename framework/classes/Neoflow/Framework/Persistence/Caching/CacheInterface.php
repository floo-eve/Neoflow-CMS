<?php

namespace Neoflow\Framework\Persistence\Caching;

interface CacheInterface {

    /**
     * Fetch cache value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key);

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
    public function store($key, $data, $ttl, array $tags);

    /**
     * Delete cache value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key);

    /**
     * Check wether cache value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key);

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear();
}
