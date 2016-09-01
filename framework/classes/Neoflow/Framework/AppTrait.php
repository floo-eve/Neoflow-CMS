<?php

namespace Neoflow\Framework;

use Neoflow\Framework\Core\AbstractService;
use Neoflow\Framework\Handler\Config;
use Neoflow\Framework\Handler\Logging\Logger;
use Neoflow\Framework\Handler\Router;
use Neoflow\Framework\Handler\Translator;
use Neoflow\Framework\HTTP\Request;
use Neoflow\Framework\HTTP\Session;
use ReflectionClass;

trait AppTrait
{

    /**
     * Get appliction
     *
     * @return App
     */
    protected function app()
    {
        return App::instance();
    }

    /**
     * Get logger.
     *
     * @return Logger
     */
    public function logger()
    {
        return $this->app()->get('logger');
    }

    /**
     * Get session.
     *
     * @return Session
     */
    public function session()
    {
        return $this->app()->get('session');
    }

    /**
     * Get router.
     *
     * @return Router
     */
    public function router()
    {
        return $this->app()->get('router');
    }

    /**
     * Get translator
     *
     * @return Translator
     */
    public function translator()
    {
        return $this->app()->get('translator');
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function request()
    {
        return $this->app()->get('request');
    }

    /**
     * Get config
     *
     * @return Config
     */
    public function config()
    {
        return $this->app()->get('config');
    }

    /**
     * Get reflection of current object
     *
     * @return ReflectionClass
     */
    public function getReflection()
    {
        return new ReflectionClass($this);
    }

    /**
     * Get service
     *
     * @param string $name
     * @return AbstractService
     */
    public function service($name)
    {
        return $this->app()->service($name);
    }
}
