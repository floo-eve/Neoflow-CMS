<?php

namespace Lahaina\Framework\Persistence\Caching;

abstract class AbstractCache
{
    /**
     * App trait.
     */
    use \Lahaina\Framework\AppTrait;

    /**
     * Key tagging trait.
     */
    use \Lahaina\Framework\Common\KeyTaggingTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = $this->fetch('cacheTags');
        $this->getLogger()->info($this->getReflection()->getShortName().' created');
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->store('cacheTags', $this->tags);
    }

    /**
     * Delete cache values by tag.
     *
     * @param string $tag
     *
     * @return bool
     */
    public function deleteByTag($tag)
    {
        $keys = $this->getKeysFromTag($tag);
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return $this->deleteTag($tag);
    }

    /**
     * Fetch cache values by tag.
     *
     * @param string $tag
     *
     * @return array
     */
    public function fetchByTag($tag)
    {
        $keys = $this->getKeysFromTag($tag);
        $cacheValues = array();
        foreach ($keys as $key) {
            $cacheValues[] = $this->fetch($key);
        }

        return $cacheValues;
    }

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
