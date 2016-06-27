<?php

namespace Lahaina\Framework\Persistence\Caching;

class DisabledCache extends AbstractCache
{
    /**
     * Fetch cache value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key)
    {
        return false;
    }

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
    public function store($key, $data, $ttl = 0, array $tags = array())
    {
        return true;
    }

    /**
     * Delete cache value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        return true;
    }

    /**
     * Check wether cache value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return false;
    }

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear()
    {
        return true;
    }
}
