<?php

namespace Neoflow\Framework\Persistence\Caching;

abstract class AbstractCache implements CacheInterface
{
    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * Key tagging trait.
     */
    use \Neoflow\Framework\Common\KeyTaggingTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = $this->fetch('cacheTags');
        $this->logger()->info($this->getReflection()->getShortName().' created');
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
     * Fetch cache value by tag.
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
     * Check wether cache value by tag exists.
     *
     * @param array $tag
     *
     * @return bool
     */
    public function existsByTag($tag)
    {
        $cacheValues = $this->fetchByTag($tag);

        return count($cacheValues) > 0;
    }
}
