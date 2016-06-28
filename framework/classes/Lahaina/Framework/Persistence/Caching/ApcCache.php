<?php

namespace Neoflow\Framework\Persistence\Caching;

class ApcCache extends AbstractCache
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
        return \apc_fetch($key);
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
        // Set key to tags
        $this->setKeyToTags($tags, $key);

        return \apc_store($key, $data, $ttl);
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
        return \apc_delete($key);
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
        return \apc_exists($key);
    }

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear()
    {
        return \apc_clear_cache();
    }
}
