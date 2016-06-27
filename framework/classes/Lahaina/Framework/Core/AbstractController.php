<?php

namespace Lahaina\Framework\Core;

use \Lahaina\Framework\HTTP\Request;
use \Lahaina\Framework\HTTP\Responsing\DebugResponse;
use \Lahaina\Framework\HTTP\Responsing\RedirectResponse;
use \Lahaina\Framework\HTTP\Responsing\Response;
use \Lahaina\Framework\HTTP\Session;
use \RuntimeException;

abstract class AbstractController
{

    /**
     * @var AbstractView
     */
    protected $view;

    /**
     * App trait.
     */
    use \Lahaina\Framework\AppTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {

        $this->setView();
    }

    /**
     * Render view as content of response.
     *
     * @param string   $viewFile
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    protected function render($viewFile, array $parameters = array(), Response $response = null)
    {

        if ($this->view instanceof AbstractView) {

            $this->view->renderView($viewFile, $parameters, true);

            $content = $this->view->renderTheme();

            if ($response === null) {

                if ($this->app()->get('config')->get('debugging')->get('debugBar')) {

                    $response = new DebugResponse();
                } else {

                    $response = new Response();
                }
            }


            return $response->setContent($content);
        }

        throw new RuntimeException('View not set or wrong type');
    }

    /**
     * Route to route.
     *
     * @param string $route
     * @param array  $args
     *
     * @return Response
     */
    protected function route($route, $args = array())
    {
        return $this->app()->get('router')
                ->route($route, $args);
    }

    /**
     * Redirect to route.
     *
     * @param string $routeKey
     * @param array  $args
     * @param int    $statusCode
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute($routeKey, $args = array(), $statusCode = 302)
    {
        $url = $this->app()->get('router')
            ->generateUrl($routeKey, $args);

        return $this->redirect($url, $statusCode);
    }

    /**
     * Redirect to url.
     *
     * @param string $url
     * @param int    $statusCode
     *
     * @return RedirectResponse
     */
    public function redirect($url, $statusCode = 302)
    {
        return new RedirectResponse($url, $statusCode);
    }

    /**
     * Pre hook method.
     *
     * @param array $args
     *
     * @return Response|false
     */
    public function preHook($args)
    {
        return false;
    }

    /**
     * Post hook method.
     *
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function postHook(Response $response, array $args)
    {
        return $response;
    }

    /**
     * Index action.
     */
    abstract public function indexAction($args);

    /**
     * Set view.
     */
    abstract protected function setView();

    /**
     * Get session
     *
     * @return Session
     */
    protected function getSession()
    {
        return $this->app()->get('session');
    }

    /**
     * Get request
     *
     * @return Request
     */
    protected function getRequest()
    {
        return $this->app()->get('request');
    }
}
