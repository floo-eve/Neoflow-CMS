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

        $cache = $this->app()->get('cache');
        $cacheKey = sha1('_viewAndTemplateFilePaths');

        if ($cache->exists($cacheKey)) {

            // Fetch tempalte and view file paths from cahce
            $viewAndTemplateFilePaths = $cache->fetch($cacheKey);
            $this->viewFilePaths = $viewAndTemplateFilePaths[0];
            $this->templateFilePaths = $viewAndTemplateFilePaths[1];
        } else {

            // Set theme template and view file paths
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

            // Store template and view file paths to cache
            $cache->store($cacheKey, array($this->viewFilePaths, $this->templateFilePaths), 0, array('_view'));
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
