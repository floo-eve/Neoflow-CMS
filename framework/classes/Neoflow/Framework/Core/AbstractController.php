<?php

namespace Neoflow\Framework\Core;

use \Neoflow\Framework\HTTP\Request;
use \Neoflow\Framework\HTTP\Responsing\DebugResponse;
use \Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use \Neoflow\Framework\HTTP\Responsing\Response;
use \Neoflow\Framework\HTTP\Session;
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
    use \Neoflow\Framework\AppTrait;

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
     *
     * @throws RuntimeException
     */
    protected function render($viewFile, array $parameters = array(), Response $response = null)
    {
        if ($this->view instanceof AbstractView) {

            $this->view->renderView($viewFile, $parameters, true);
            $content = $this->view->renderTheme();

            if ($response === null) {
                if ($this->config()->get('debugging')->get('debugBar')) {
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
     * @param string $routeKey
     * @param array  $args
     *
     * @return Response
     */
    protected function route($routeKey, $args = array())
    {
        return $this->router()
                ->routeByKey($routeKey, $args);
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
        return $this->redirect(generate_url($routeKey, $args), $statusCode);
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
     * Set new session flash value.
     * @param string $key
     * @param mixed $value
     * @return Session
     */
    protected function setFlash($key, $value)
    {
        return $this->session()->setFlash($key, $value);
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
