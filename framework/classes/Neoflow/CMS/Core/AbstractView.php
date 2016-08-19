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

        $cacheKey = sha1('_viewAndtemplateFileDirectories');
        if ($this->getCache()->exists($cacheKey)) {

            // Fetch tempalte and view file paths from cahce
            $viewAndTemplateFilePaths = $this->getCache()->fetch($cacheKey);
            $this->viewFileDirectories = $viewAndTemplateFilePaths[0];
            $this->templateFileDirectories = $viewAndTemplateFilePaths[1];
        } else {

            // Set theme template and view file paths
            $this->viewFileDirectories[] = $this->getThemePath(DIRECTORY_SEPARATOR.'views');
            $this->templateFileDirectories[] = $this->getThemePath(DIRECTORY_SEPARATOR.'templates');

            // Set template and view file paths of modules
            $modules = $this->app()->get('modules');
            foreach ($modules as $module) {
                $this->viewFileDirectories[] = $module->getPath(DIRECTORY_SEPARATOR.'views');
                $this->templateFileDirectories[] = $module->getPath(DIRECTORY_SEPARATOR.'templates');
            }

            // Store template and view file paths to cache
            $this->getCache()->store($cacheKey, array($this->viewFileDirectories, $this->templateFileDirectories), 0, array('_view'));
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
