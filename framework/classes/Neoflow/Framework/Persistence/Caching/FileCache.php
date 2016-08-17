<?php

namespace Neoflow\Framework\Persistence\Caching;

class FileCache extends AbstractCache
{

    /**
     * @var string
     */
    protected $fileCacheFolder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fileCacheFolder = $this->config()->getTempPath(DIRECTORY_SEPARATOR . 'cache');
        if (!is_dir($this->fileCacheFolder)) {
            mkdir($this->fileCacheFolder);
        }

        parent::__construct();
    }

    /**
     * Get file name.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getFileName($key)
    {
        return $this->fileCacheFolder . DIRECTORY_SEPARATOR . 'cache_' . sha1($key);
    }

    /**
     * Fetch cache value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key)
    {
        $cacheFile = $this->getFileName($key);

        if (!file_exists($cacheFile)) {
            return false;
        }

        $data = file_get_contents($cacheFile);
        $data = unserialize($data);
        // Delete file if unserializing didn't work or cache is expired
        if (count($data) !== 2 || time() > $data[0]) {
            unlink($cacheFile);

            return false;
        }

        return $data[1];
    }

    /**
     * Store cache value.
     *
     * @param string $key
     * @param mixed  $data
     * @param int    $ttl
     * @param array  $tags
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function store($key, $data, $ttl = 0, array $tags = array())
    {
        // Set key to tags
        $this->setKeyToTags($tags, $key);
        // Opening the file in read/write mode
        $cacheFile = $this->getFileName($key);
        $handle = fopen($cacheFile, 'w+');
        if (!$handle) {
            throw new \Exception('Could not write to cache file');
        }

        if ($ttl === 0) {
            $ttl = 31536000 * 10; // 10 years, like infinite :)
        }
        // Serializing data and TTL
        fwrite($handle, serialize(array(time() + $ttl, $data)));
        fclose($handle);
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
        $cacheFile = $this->getFileName($key);
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }

        return false;
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
        return (bool) $this->fetch($key);
    }

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear()
    {
        $cacheFiles = scandir($this->fileCacheFolder);
        foreach ($cacheFiles as $cacheFile) {
            $cacheFile = $this->fileCacheFolder . DIRECTORY_SEPARATOR . $cacheFile;
            if (is_file($cacheFile)) {
                unlink($cacheFile);
            }
        }

        return true;
    }
}
