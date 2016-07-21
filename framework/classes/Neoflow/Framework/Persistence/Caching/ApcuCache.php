<?php

namespace Neoflow\Framework\Persistence\Caching;

class ApcuCache extends AbstractCache
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
        return \apcu_fetch($key);
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

        return \apcu_store($key, $data, $ttl);
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
        return \apcu_delete($key);
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
        return \apcu_exists($key);
    }

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear()
    {
        return \apcu_clear_cache();
    }
}
