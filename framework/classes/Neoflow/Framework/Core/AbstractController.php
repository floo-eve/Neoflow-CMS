<?php

namespace Neoflow\Framework\Core;

use Neoflow\Framework\HTTP\Responsing\DebugResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\HTTP\Session;
use RuntimeException;
use function generate_url;

abstract class AbstractController
{

    /**
     * @var AbstractView
     */
    protected $view;

    /**
     * @var Response
     */
    protected $response;

    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * Constructor.
     *
     * @param AbstractView $view
     */
    public function __construct(AbstractView $view = null)
    {
        if ($view) {
            $this->view = $view;
        } else {
            $this->initView();
        }
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
     * Get response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
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
     * Initialize view.
     */
    abstract protected function initView();

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
}
