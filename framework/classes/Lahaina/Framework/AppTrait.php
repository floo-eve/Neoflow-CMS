<?php

namespace Lahaina\Framework;

use \Lahaina\Framework\Handler\Logging\Logger;
use \Lahaina\Framework\Handler\Router;
use \Lahaina\Framework\Persistence\Database;
use \Lahaina\Framework\Persistence\ORM;
use \ReflectionClass;

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
    public function getLogger()
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
}
