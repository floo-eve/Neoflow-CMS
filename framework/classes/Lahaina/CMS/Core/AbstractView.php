<?php

namespace Neoflow\CMS\Core;

use Neoflow\CMS\Model\ThemeModel;

abstract class AbstractView extends \Neoflow\Framework\Core\AbstractView
{

    /**
     * @var ThemeModel
     */
    protected $theme;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTheme();

        $this->app()->set('theme', $this->theme);
    }

    /**
     * Get theme url.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getThemeUrl($uri = '')
    {
        return $this->theme->getUrl($uri);
    }

    /**
     * Get theme path.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function getThemePath($uri = '')
    {
        return $this->theme->getPath($uri);
    }

    /**
     * Set theme.
     */
    abstract protected function setTheme();
}
