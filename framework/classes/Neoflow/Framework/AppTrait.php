<?php

namespace Neoflow\Framework;

use \Neoflow\Framework\Handler\Logging\Logger;
use \Neoflow\Framework\Handler\Router;
use \Neoflow\Framework\Persistence\Database;
use \Neoflow\Framework\Persistence\ORM;
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
