<?php

namespace Neoflow\Framework\Core;

use Exception;
use InvalidArgumentException;
use Neoflow\CMS\Core\View;
use Neoflow\Framework\Common\Container;
use Neoflow\Framework\Handler\Config;
use Neoflow\Framework\Handler\Translator;
use Neoflow\Framework\Handler\Validation\ValidationHelper;
use Neoflow\Framework\Persistence\Caching\AbstractCache;

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
     * @var array
     */
    protected $viewFileDirectories = array();

    /**
     * @var array
     */
    protected $templateFileDirectories = array();

    /**
     * @var array
     */
    protected $resources = array(
        'stylesheets' => array(),
        'scripts' => array(),
    );

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->data = new Container();

        $this->viewFileDirectories[] = $this->getConfig()->getPath(DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'views');
        $this->templateFileDirectories[] = $this->getConfig()->getPath(DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'templates');
    }

    /**
     * Add resource url.
     *
     * @param string $url
     * @param string $type
     * @param string $key
     * @param bool   $isRelative
     *
     * @return self
     *
     * @throw InvalidArgumentException
     */
    protected function addResource($url, $type, $key = 'default', $isRelative = true)
    {
        if ($type === 'stylesheets' || $type === 'scripts') {
            if (!isset($this->resources[$type][$key])) {
                $this->resources[$type][$key] = array();
            }
            if ($isRelative) {
                $url = $this->getThemeUrl('/'.$url);
            }
            $this->resources[$type][$key][] = $url;

            return $this;
        }
        throw new InvalidArgumentException('Type of resource has to be "stylesheet" or "script"');
    }

    /**
     * Add resource urls.
     *
     * @param array  $urls
     * @param string $type
     * @param string $key
     * @param bool   $isRelative
     *
     * @return self
     */
    protected function addResources(array $urls, $type, $key = 'default', $isRelative = true)
    {
        foreach ($urls as $url) {
            $this->addResource($url, $type, $key, $isRelative);
        }

        return $this;
    }

    /**
     * Add stylesheet url.
     *
     * @param string $url
     * @param string $key
     * @param bool   $isRelative
     *
     * @return self
     */
    public function addStylesheet($url, $key = 'default', $isRelative = true)
    {
        return $this->addResource($url, 'stylesheets', $key, $isRelative);
    }

    /**
     * Add stylesheet urls.
     *
     * @param string $urls
     * @param string $key
     * @param bool   $isRelative
     *
     * @return self
     */
    public function addStylesheets($urls, $key = 'default', $isRelative = true)
    {
        return $this->addResources($urls, 'stylesheets', $key, $isRelative);
    }

    /**
     * Add script url.
     *
     * @param string $url
     * @param string $key
     * @param bool   $isRelative
     *
     * @return self
     */
    public function addScript($url, $key = 'default', $isRelative = true)
    {
        return $this->addResource($url, 'scripts', $key, $isRelative);
    }

    /**
     * Add script urls.
     *
     * @param string $urls
     * @param string $key
     * @param bool   $isRelative
     *
     * @return self
     */
    public function addScripts($urls, $key = 'default', $isRelative = true)
    {
        return $this->addResources($urls, 'scripts', $key, $isRelative);
    }

    /**
     * Render resources.
     *
     * @param string $type
     * @param string $template
     * @param string $key
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function renderResources($type, $template, $key = 'default')
    {
        $output = '';

        $urls = $this->getResourceUrls($type, $key);
        foreach ($urls as $url) {
            $output .= sprintf($template, $url).PHP_EOL;
        }

        return $output;
    }

    /**
     * Get resource urls.
     *
     * @param string $type
     * @param string $key
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function getResourceUrls($type, $key = 'default')
    {
        if ($type === 'scripts' || $type === 'stylesheets') {
            if (isset($this->resources[$type][$key])) {
                return $this->resources[$type][$key];
            }

            return array();
        }
        throw new InvalidArgumentException('Type of resource has to be "stylesheet" or "script"');
    }

    /**
     * Get script urls.
     *
     * @param string $key
     *
     * @return array
     */
    public function getScriptUrls($key = 'default')
    {
        return $this->getResourceUrls('scripts', $key);
    }

    /**
     * Get stylesheet urls.
     *
     * @param string $key
     *
     * @return array
     */
    public function getStylesheetUrls($key = 'default')
    {
        return $this->getResourceUrls('stylesheets', $key);
    }

    /**
     * Render scripts.
     *
     * @param string $key
     *
     * @return string
     */
    public function renderScripts($key = 'default')
    {
        return $this->renderResources('scripts', '<script src="%s"></script>', $key);
    }

    /**
     * Render stylesheets.
     *
     * @param string $key
     *
     * @return string
     */
    public function renderStylesheets($key = 'default')
    {
        return $this->renderResources('stylesheets', '<link href="%s" rel="stylesheet" type="text/css" />', $key);
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
        return $this->getConfig()->getUrl('/theme/'.$uri);
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
        return $this->getConfig()->getPath('/theme/'.$uri);
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
            throw new Exception('A block already started with this ID: '.$id);
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
     * @throws Exception
     */
    public function renderView($viewFile, $parameters = array(), $strict = false)
    {
        $this->addParameters($parameters);

        $viewFilePath = $this->getFilePath($viewFile, sha1('_view_'.$viewFile), $this->viewFileDirectories);
        if ($viewFilePath) {
            $content = $this->renderFile($viewFilePath, $parameters);

            if ($strict && !$this->getBlock(1)) {
                $this->addContentToBlock(1, $content);
            }

            return $content;
        }
        throw new Exception('View not found: '.$viewFile);
    }

    /**
     * Render template file to html output.
     *
     * @param string $templateFile Template file name
     * @param array parameters Parameters for the template content
     *
     * @return string
     *
     * @throws Exception
     */
    public function renderTemplate($templateFile, $parameters = array())
    {
        $templateFilePath = $this->getFilePath($templateFile, sha1('_template_'.$templateFile), $this->templateFileDirectories);
        if ($templateFilePath) {
            return $this->renderFile($templateFilePath, $parameters);
        }
        throw new Exception('Template not found: '.$templateFile);
    }

    /**
     * Get file path.
     *
     * @param string $file        View or template file name
     * @param string $cacheKey    Cache key
     * @param array  $directories View or template directories
     *
     * @return bool
     */
    protected function getFilePath($file, $cacheKey, $directories)
    {
        if ($this->getCache()->exists($cacheKey)) {
            return $this->getCache()->fetch($cacheKey);
        } else {
            foreach ($directories as $directory) {
                $filePaths = array_map(function ($extension) use ($directory, $file) {
                    return $directory.DIRECTORY_SEPARATOR.$file.$extension;
                }, array('', '.php', '.html'));

                foreach ($filePaths as $filePath) {
                    if (is_file($filePath)) {
                        $this->getCache()->store($cacheKey, $filePath, 0, array('_view'));

                        return $filePath;
                    }
                }
            }
        }

        return false;
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
            $this->getThemePath(DIRECTORY_SEPARATOR.$themeFile),
            $this->getThemePath(DIRECTORY_SEPARATOR.$themeFile.'.php'),
            $this->getThemePath(DIRECTORY_SEPARATOR.$themeFile.'.html'),
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
     * @throws InvalidArgumentException
     */
    public function renderFile($file, array $parameters = array())
    {
        if (is_file($file)) {
            $parameters['view'] = $this;

            $output = call_user_func(function () use ($file, $parameters) {
                extract($parameters);
                ob_start();
                ob_implicit_flush(false);
                include $file;
                $output = ob_get_contents();
                ob_end_clean();

                return $output;
            });

            // Search and replace placeholders
            foreach ($parameters as $key => $value) {
                if (is_string($value) || is_integer($value)) {
                    $output = str_replace('['.$key.']', $value, $output);
                }
            }

            return $output;
        }
        throw new InvalidArgumentException('File not found: '.$file);
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
     * Check wether validation error exists.
     *
     * @param string $key
     * @param mixed  $returnValue
     *
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
     * Check wether route is active.
     *
     * @param array|string $routeKeys
     * @param mixed        $returnValue
     * @param mixed        $returnFailedValue
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
     * Get config.
     *
     * @return Config
     */
    protected function getConfig()
    {
        return $this->app()->get('config');
    }

    /**
     * Get cache.
     *
     * @return AbstractCache
     */
    protected function getCache()
    {
        return $this->app()->get('cache');
    }

    /**
     * Get config.
     *
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->app()->get('translator');
    }
}
