<?php

namespace Neoflow\CMS\Handler;

use \Exception;
use \Neoflow\Framework\HTTP\Responsing\Response;

class Router extends \Neoflow\Framework\Handler\Router
{

    /**
     * Load routes
     *
     * @return self
     */
    public function loadRoutes()
    {
        // Run route files of modules
        $modules = $this->app()->get('modules');
        foreach ($modules as $module) {
            $moduleRoutesFile = $module->getPath('/routes.php');
            $this->runRouteFile($moduleRoutesFile);
        }

        parent::loadRoutes();

        // Add frontend index route to the end of the array
        $this->addRoute(array('frontend_index', 'any', '/(slug:uri)', 'Frontend@index'));

        return $this;
    }

    /**
     * Start routing.
     *
     * @return \Neoflow\CMS\Core\AbstractController
     */
    public function startRouting()
    {
        try {
            return parent::startRouting();
        } catch (Exception $ex) {

            while (ob_get_level() > 1) {
                ob_end_clean();
            }
            $this->logger()->error($ex->getMessage(), array(
                'code' => $ex->getCode(),
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
                'stack trace' => $ex->getTraceAsString())
            );

            $errorRoute = $this->getRouteByKey('frontend_error');
            return $this->route($errorRoute, array($ex));
        }
    }
}
