<?php

namespace Neoflow\Framework\Handler;

use Neoflow\Framework\Common\Container;

class Config extends Container
{

    /**
     * Get url.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getUrl($uri = '')
    {
        return normalize_url($this->get('url') . '/' . $uri);
    }

    /**
     * Get path.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getPath($uri = '')
    {

        return normalize_path($this->get('path') . DIRECTORY_SEPARATOR . $uri);
    }

    /**
     * Get temp folder path
     *
     * @param string $uri
     *
     * @return string
     */
    public function getTempPath($uri = '')
    {
        return $this->getPath(DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $uri);
    }

    /**
     * Get temp folder url
     *
     * @param string $uri
     *
     * @return string
     */
    public function getTempUrl($uri = '')
    {
        return $this->getUrl('/temp/' . $uri);
    }
}
