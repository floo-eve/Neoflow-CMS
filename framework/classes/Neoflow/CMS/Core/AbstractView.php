<?php

namespace Neoflow\CMS\Core;

use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\Framework\ORM\EntityCollection;

abstract class AbstractView extends \Neoflow\Framework\Core\AbstractView
{

    /**
     * @var ThemeModel
     */
    protected $theme;

    /**
     * @var LanguageModel
     */
    protected $activeLanguage;

    /**
     * @var EntityCollection
     */
    protected $languages;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Initialize theme
        $this->initTheme();

        if ($this->getCache()->existsByTag('_view')) {

            // Fetch template and view file directories from cache
            $this->viewFileDirectories = $this->getCache()->fetch('viewFileDirectories');
            $this->templateFileDirectories = $this->getCache()->fetch('templateFileDirectories');
        } else {

            // Set theme template and view file directories
            $this->viewFileDirectories[] = $this->getThemePath(DIRECTORY_SEPARATOR . 'views');
            $this->templateFileDirectories[] = $this->getThemePath(DIRECTORY_SEPARATOR . 'templates');

            // Set template and view file directories of modules
            $modules = $this->app()->get('modules');
            foreach ($modules as $module) {
                $this->viewFileDirectories[] = $module->getPath(DIRECTORY_SEPARATOR . 'views');
                $this->templateFileDirectories[] = $module->getPath(DIRECTORY_SEPARATOR . 'templates');
            }

            // Store template and view file directories to cache
            $this->getCache()->store('viewFileDirectories', $this->viewFileDirectories, 0, array('_view'));
            $this->getCache()->store('templateFileDirectories', $this->templateFileDirectories, 0, array('_view'));
        }

        // Set languages
        $this->languages = LanguageModel::repo()
            ->where('is_active', '=', true)
            ->fetchAll();

        // Set active language
        $activeLanguageCode = $this->translator()->getActiveLanguageCode();
        $this->activeLanguage = LanguageModel::findByColumn('code', $activeLanguageCode);
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
     * Get languages.
     *
     * @return EntityCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Get active language.
     *
     * @return LanguageModel
     */
    public function getActiveLanguage()
    {
        return $this->activeLanguage;
    }

    /**
     * Initialize theme.
     */
    abstract protected function initTheme();
}
