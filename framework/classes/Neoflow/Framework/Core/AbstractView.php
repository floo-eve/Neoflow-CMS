<?php

namespace Neoflow\Framework\Core;

use \Exception;
use \InvalidArgumentException;
use \Neoflow\CMS\Core\View;
use \Neoflow\Framework\Common\Container;
use \Neoflow\Framework\Handler\Config;
use \Neoflow\Framework\Handler\Presentation;
use \Neoflow\Framework\Handler\Translator;
use \Neoflow\Framework\Handler\Validation\ValidationHelper;

abstract class AbstractView
{

    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var Container
     */
    protected $data;

    /**
     * @var array
     */
    protected $blocks = array();

    /**
     * @var array
     */
    protected $openBlocks = array();

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->data = new Container();
    }

    /**
     * Get view value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->data->get($key, $default);
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
        return $this->getConfig()->getUrl('/theme/' . $uri);
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
        return $this->getConfig()->getPath('/theme/' . $uri);
    }

    /**
     * Set view value.
     *
     * @param string $key
     * @param mixed  $value
     * @param bool   $translate
     *
     * @return View
     */
    public function set($key, $value, $translate = false)
    {
        if ($translate) {
            $value = $this->translate($value);
        }
        $this->data->set($key, $value);

        return $this;
    }

    /**
     * Start buffering block.
     *
     * @param string|int $id
     *
     * @throws Exception
     */
    public function startBlock($id)
    {
        if (in_array($id, $this->openBlocks)) {
            throw new Exception('A block already started with this ID: ' . $id);
        }
        $this->openBlocks[] = $id;
        $this->blocks[$id] = '';
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * Stop buffering block.
     *
     * @throws Exception
     */
    public function stopBlock()
    {
        if (count($this->openBlocks) === 0) {
            throw new Exception('No block started.');
        }

        $id = array_pop($this->openBlocks);

        $this->blocks[$id] = ob_get_contents();
        ob_end_clean();
    }

    /**
     * Get block content.
     *
     * @param string|int $id
     * @param string     $default
     *
     * @return string
     */
    public function getBlock($id, $default = '')
    {
        if ($this->hasBlock($id)) {
            return $this->blocks[$id];
        }

        return $default;
    }

    /**
     * Check wether block content exists.
     *
     * @param string|int $id
     *
     * @return bool
     */
    public function hasBlock($id)
    {
        return isset($this->blocks[$id]);
    }

    /**
     * Set block content.
     *
     * @param string|int $id
     * @param string     $content
     *
     * @return View
     */
    public function setBlock($id, $content)
    {
        $this->blocks[$id] = $content;

        return $this;
    }

    /**
     * Add content to block.
     *
     * @param string|int $id
     * @param string     $content
     *
     * @return View
     */
    public function addContentToBlock($id, $content)
    {
        if (isset($this->blocks[$id])) {
            $this->blocks[$id] .= $content;
        } else {
            $this->setBlock($id, $content);
        }

        return $this;
    }

    /**
     * Render view file to html output.
     *
     * @param string $viewFile   View file name
     * @param array  $parameters Parameters for the view content
     * @param bool   $strict     If TRUE set rendered view content to the first block
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function renderView($viewFile, $parameters = array(), $strict = false)
    {
        // Get view file paths
        $viewFilePaths = $this->getViewFilePaths($viewFile);

        $this->addParameters($parameters);

        // Render first view file which exists
        foreach ($viewFilePaths as $viewFilePath) {
            if (is_file($viewFilePath)) {
                $content = $this->renderFile($viewFilePath, $parameters);

                if ($strict && !$this->getBlock(1)) {
                    $this->addContentToBlock(1, $content);
                }

                return $content;
            }
        }

        throw new InvalidArgumentException('View not found: ' . $viewFile);
    }

    /**
     * Get view file paths.
     *
     * @param string $view View name
     *
     * @return array
     */
    protected function getViewFilePaths($view)
    {
        return array(
            $this->getConfig()->getPath('/application/views/' . $view),
            $this->getConfig()->getPath('/application/views/' . $view . '.php'),
            $this->getConfig()->getPath('/application/views/' . $view . '.html'),
            $this->getThemePath('/views/' . $view),
            $this->getThemePath('/views/' . $view . '.php'),
            $this->getThemePath('/views/' . $view . '.html'),
        );
    }

    /**
     * Render template file to html output.
     *
     * @param string $templateFile Template file name
     * @param array parameters Parameters for the template content
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function renderTemplate($templateFile, $parameters = array())
    {
        // Get template file paths
        $templateFilePaths = $this->getTemplateFilePaths($templateFile);

        $this->addParameters($parameters);

        // Render first template file which exists
        foreach ($templateFilePaths as $templateFilePath) {
            if (is_file($templateFilePath)) {
                return $this->renderFile($templateFilePath, $parameters);
            }
        }

        throw new InvalidArgumentException('Template not found: ' . $templateFile);
    }

    /**
     * Get template file paths.
     *
     * @param string $templateFile Template file name
     *
     * @return array
     */
    protected function getTemplateFilePaths($templateFile)
    {
        return array(
            $this->getConfig()->getPath('/application/templates/' . $templateFile),
            $this->getConfig()->getPath('/application/templates/' . $templateFile . '.php'),
            $this->getConfig()->getPath('/application/templates/' . $templateFile . '.html'),
            $this->getThemePath('/templates/' . $templateFile),
            $this->getThemePath('/templates/' . $templateFile . '.php'),
            $this->getThemePath('/templates/' . $templateFile . '.html'),
        );
    }

    /**
     * Render theme to html output.
     *
     * @param string $themeFile
     *
     * @return string
     *
     * @throws Exception
     */
    public function renderTheme($themeFile = 'index')
    {
        $themeFiles = array(
            $this->getThemePath($themeFile),
            $this->getThemePath($themeFile . '.php'),
            $this->getThemePath($themeFile . '.html'),
        );

        // Render theme file and get output
        foreach ($themeFiles as $themeFile) {
            if (is_file($themeFile)) {
                ob_start();
                return $this->renderFile($themeFile, $this->parameters);
            }
        }
    }

    protected function addParameters($parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Generate url of route.
     *
     * @param string $routeKey
     * @param array  $args
     * @param string $languageCode
     *
     * @return string
     */
    public function generateUrl($routeKey, $args = array(), $languageCode = '')
    {
        return $this->app()->get('router')->generateUrl($routeKey, $args, $languageCode);
    }

    /**
     * Render file to output html.
     *
     * @param string $file
     * @param array  $parameters
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function renderFile($file, array $parameters = array())
    {
        if (is_file($file)) {
            extract($parameters);

            ob_start();
            ob_implicit_flush(false);
            include $file;
            $output = ob_get_contents();
            ob_end_clean();

            // Search and replace placeholders
            foreach ($parameters as $key => $value) {
                if (is_string($value) || is_integer($value)) {
                    $output = str_replace('[' . $key . ']', $value, $output);
                }
            }

            return $output;
        }
        throw new \InvalidArgumentException('File not found: ' . $file);
    }

    /**
     * Translate key.
     *
     * @param string $key
     * @param array  $values
     *
     * @return string
     */
    public function translate($key, $values = array())
    {
        return $this->getTranslator()->translate($key, $values);
    }

    /**
     * Check wether validation error exists
     *
     * @param string $key
     * @param mixed $returnValue
     * @return mixed
     */
    public function hasValidationError($key = '', $returnValue = true)
    {
        $validationHelper = new ValidationHelper();
        if ($validationHelper->hasError($key)) {
            return $returnValue;
        }
        return false;
    }

    /**
     * Check wether route is active
     *
     * @param array|string  $routeKeys
     * @param mixed $returnValue
     * @param mixed $returnFailedValue
     *
     * @return mixed
     */
    public function isCurrentRoute($routeKeys, $returnValue = true, $returnFailedValue = false)
    {
        $currentRouting = $this->app()->get('router')->getCurrentRouting();
        $currentRouteKey = $currentRouting[0][0];

        if (is_string($routeKeys)) {
            $routeKeys = array($routeKeys);
        }

        foreach ($routeKeys as $routeKey) {
            if (fnmatch($routeKey, $currentRouteKey)) {
                return $returnValue;
            }
        }
        return $returnFailedValue;
    }

    /**
     * Get config
     *
     * @return Config
     */
    protected function getConfig()
    {
        return $this->app()->get('config');
    }

    /**
     * Get presentation
     *
     * @return Presentation
     */
    protected function getPresentation()
    {
        return $this->app()->get('presentation');
    }

    /**
     * Get config
     *
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->app()->get('translator');
    }
}
