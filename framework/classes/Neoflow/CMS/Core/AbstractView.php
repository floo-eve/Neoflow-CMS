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

        $themePath = $this->getThemePath();
        $this->setTemplateFilePath($themePath . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);
        $this->setViewFilePath($themePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);

        // Set template and view file paths of modules
        $modules = $this->app()->get('modules');
        foreach ($modules as $module) {
            $templateFilePath = $this->getConfig()->getPath(DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module->folder . DIRECTORY_SEPARATOR . 'templates');
            $this->setTemplateFilePath($templateFilePath);
            $viewFilePath = $this->getConfig()->getPath(DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module->folder . DIRECTORY_SEPARATOR . 'views');
            $this->setViewFilePath($viewFilePath);
        }
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
