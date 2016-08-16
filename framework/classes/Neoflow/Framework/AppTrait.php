<?php

namespace Neoflow\Framework;

use Neoflow\Framework\Core\AbstractService;
use Neoflow\Framework\Handler\Logging\Logger;
use Neoflow\Framework\Handler\Router;
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
     * Get router.
     *
     * @return Router
     */
    public function router()
    {
        return $this->app()->get('router');
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
