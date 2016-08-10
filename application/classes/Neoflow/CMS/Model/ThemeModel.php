<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Handler\Config;
use Neoflow\Framework\ORM\AbstractEntityModel;

class ThemeModel extends AbstractEntityModel
{
    /**
     * @var string
     */
    public static $tableName = 'themes';

    /**
     * @var string
     */
    public static $primaryKey = 'theme_id';

    /**
     * @var array
     */
    public static $properties = ['theme_id', 'title', 'folder', 'type'];

    /**
     * Get theme url.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getUrl($uri = '')
    {
        return $this
                ->getConfig()
                ->getThemesUrl('/'.$this->folder.'/'.$uri);
    }

    /**
     * Get theme path.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getPath($uri = '')
    {
        return $this
                ->getConfig()
                ->getThemesPath('/'.$this->folder.'/'.$uri);
    }

    /**
     * Get config.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->app()->get('config');
    }
}
