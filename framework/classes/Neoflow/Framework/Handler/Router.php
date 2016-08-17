<?php

namespace Neoflow\Framework\Handler;

use \InvalidArgumentException;
use \Neoflow\Framework\HTTP\Request;
use \Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use \Neoflow\Framework\HTTP\Responsing\Response;
use \RuntimeException;

class Router
{

    /**
     * App trait
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var array
     */
    protected $routes = array();

    /**
     * @var array
     */
    protected $currentRouting = array();

    /**
     * @var array
     */
    protected $namespaces = array();

    /**
     * @var string
     */
    protected $routeUriRegexPattern = '/\(([a-zA-Z0-9\-\_]+)\:(any|num|string|uri)\)/';

    /**
     * Load routes
     *
     * @return Router
     */
    public function loadRoutes()
    {
        $routeFiles = $this->config()->getPath('/application/routes.php');
        $this->runRouteFile($routeFiles);

        return $this;
    }

    /**
     * Add single route.
     *
     * @param array $route
     *
     * @return Router
     */
    public function addRoute($route)
    {
        if (count($route) === 4) {
            $this->routes[] = $route;
        }

        return $this;
    }

    /**
     * Add routes.
     *
     * @param array $routes
     *
     * @return Router
     */
    public function addRoutes($routes)
    {
        $this->routes = array_merge($this->routes, $routes);

        return $this;
    }

    /**
     * Add single namespace.
     *
     * @param array $namespace
     */
    public function addNamespace($namespace)
    {
        $this->namespaces[] = $namespace;

        return $this;
    }

    /**
     * Run route file.
     *
     * @param string $routesFile
     *
     * @return \Neoflow\CMS\Handler\Router;
     */
    public function runRouteFile($routesFile)
    {
        if (is_file($routesFile)) {
            include $routesFile;
        }

        return $this;
    }

    /**
     * Start routing.
     *
     * @return Response
     */
    public function startRouting()
    {
        $this->logger()->info('Start router');

        $cache = $this->app()->get('cache');

        $requestUri = $this->getRequest()->getUri();

        // Remove end-slash from URL
        if (substr($requestUri, -1) === '/') {
            $requestUri = rtrim($requestUri, '/');
            return new RedirectResponse($this->config()->getUrl($requestUri));
        }

        $cacheKey = sha1($requestUri);
        if ($cache->exists($cacheKey)) {
            $routing = $cache->fetch($cacheKey);
        } else {
            foreach ($this->routes as $route) {

                $args = array();
                $routeMethods = explode('|', $route[1]);
                $routeUri = $route[2];

                if ($routeUri && strtolower($routeMethods[0]) === 'any' || $this->getRequest()->isMethod($routeMethods)) {

                    // Get args of routeUri
                    $routeUriArgs = $this->getRouteUriArgs($routeUri);

                    // Create regexCode of routeUri
                    $routeUriRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[string|any]+\)/', '([a-zA-Z0-9\-\.\_\~\:\?\#\[\]\@\!\$\&\'\(\)\*\<\+\,\;\=]+)', $routeUri);
                    $routeUriRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[num]+\)/', '([0-9\.\,]+)', $routeUriRegex);
                    $routeUriRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[uri]+\)/', '(.*)', $routeUriRegex);
                    $routeUriRegex = str_replace(array('/'), array('\/'), $routeUriRegex);
                    $routeUriRegex = '/^' . $routeUriRegex . '$/';

                    // Remove args of routeUri
                    $routeUri = str_replace('//', '/', preg_replace($this->routeUriRegexPattern, '', $routeUri));

                    // Check if routeUri (regexCode) is matching requestUri
                    if (preg_match($routeUriRegex, $requestUri) && strpos($requestUri, $routeUri) === 0) {
                        $requestUri = substr($requestUri, strlen($routeUri));

                        $requestUriParts = array_values(array_filter(explode('/', $requestUri)));

                        if (isset($routeUriArgs[0])) {
                            for ($i = 0; $i < count($routeUriArgs[0]); ++$i) {
                                if (isset($requestUriParts[$i])) {
                                    if ($routeUriArgs[2][$i] === 'num' && is_numeric($requestUriParts[$i])) {
                                        if (is_float($requestUriParts[$i])) {
                                            $args[$routeUriArgs[1][$i]] = (float) $requestUriParts[$i];
                                        } else {
                                            $args[$routeUriArgs[1][$i]] = (int) $requestUriParts[$i];
                                        }
                                    } elseif ($routeUriArgs[2][$i] === 'string' && is_string($requestUriParts[$i])) {
                                        $args[$routeUriArgs[1][$i]] = $requestUriParts[$i];
                                    } elseif ($routeUriArgs[2][$i] === 'uri') {
                                        $args[$routeUriArgs[1][$i]] = $requestUri;
                                        break;
                                    } elseif ($routeUriArgs[2][$i] === 'any') {
                                        $args[$routeUriArgs[1][$i]] = $requestUriParts[$i];
                                    } else {
                                        continue;
                                    }
                                } else {
                                    continue;
                                }
                            }
                        }
                        $routing = array($route, $args);
                        break;
                    }
                }
            }
            if (!isset($routing)) {
                throw new RuntimeException('No matching route for current URI found: ' . $requestUri);
            }
            $cache->store($cacheKey, $routing, 0, array('_route'));
        }

        // Check wether language code not found, not sent if needed or sent if not needed
        $uriLanguageCode = $this->getRequest()->getUriLanguage();
        $languageCodes = $this->config()->get('languages');
        if (($uriLanguageCode && !in_array($uriLanguageCode, $languageCodes)) ||
            (count($languageCodes) > 1 && !$uriLanguageCode) ||
            (count($languageCodes) === 1 && $uriLanguageCode)) {
            $url = $this->generateUrl($routing[0][0], $routing[1]);
            return new RedirectResponse($url);
        }

        return $this->route($routing[0], $routing[1]);
    }

    /**
     * Get route by key.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    public function getRouteByKey($key)
    {
        foreach ($this->routes as $index => $route) {
            if ($route[0] === $key) {
                return $this->routes[$index];
            }
        }

        throw new InvalidArgumentException('Route key not found: ' . $key);
    }

    /**
     * Route to controller and action with key of route
     *
     * @param string $routeKey
     * @param array $args
     * @return Response
     */
    public function routeByKey($routeKey, array $args)
    {
        $route = $this->getRouteByKey($routeKey);
        return $this->route($route, $args);
    }

    /**
     * Route to controller and action.
     *
     * @param array $route
     * @param array $args
     *
     * @throws RuntimeException
     *
     * @return Response
     */
    public function route(array $route, array $args)
    {
        $this->currentRouting = array('route' => $route, 'args' => $args);

        $routePathParts = $this->getRoutePathParts($route[3]);

        foreach ($this->namespaces as $namespace) {
            $controllerClass = $routePathParts['controllerClass'];
            $actionMethod = $routePathParts['actionMethod'];

            $controllerClass = $namespace . $controllerClass;

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass($this->app());
                if (method_exists($controller, $actionMethod)) {
                    $response = $controller->preHook($args);
                    if (!$response) {
                        $response = $controller->$actionMethod($args);
                    }
                    return $controller->postHook($response, $args);
                }
            }
        }

        throw new RuntimeException('Controller class and action method for route not found: ' . $routePathParts['controllerClass'] . '@' . $routePathParts['actionMethod']);
    }

    /**
     * Get active route.
     *
     * @return mixed
     */
    public function getCurrentRouting($key = null)
    {
        if (isset($this->currentRouting[$key])) {
            return $this->currentRouting[$key];
        }
        return $this->currentRouting;
    }

    /**
     * Get the controller and action name of the route path.
     *
     * @param string $routePath
     * @param string $defaultActionMethod
     *
     * @return array
     */
    public function getRoutePathParts($routePath, $defaultActionMethod = 'indexAction')
    {
        $routePathParts = explode('@', $routePath);
        $result = array(
            'controllerClass' => $routePathParts[0] . 'Controller',
            'actionMethod' => $defaultActionMethod,
        );
        if (isset($routePathParts[1])) {
            $result['actionMethod'] = $routePathParts[1] . 'Action';
        }

        return $result;
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
        $routeUri = '';
        if ($routeKey) {
            $route = $this->getRouteByKey($routeKey);
        } else {
            $route = $this->getCurrentRouting('route');
            $args = $this->getCurrentRouting('args');
        }
        if ($route) {
            $routeUri = $route[2];

            if (count($args)) {
                $routeUriArgs = $this->getRouteUriArgs($routeUri);

                if (isset($routeUriArgs[1])) {
                    for ($i = 0; $i < count($routeUriArgs[1]); ++$i) {
                        if (isset($args[$routeUriArgs[1][$i]])) {
                            $pattern = '/\([' . preg_quote($routeUriArgs[1][$i]) . ']+\:[any|num|string|uri]+\)/';
                            $routeUri = preg_replace($pattern, $args[$routeUriArgs[1][$i]], $routeUri);
                            unset($args[$routeUriArgs[1][$i]]);
                        }
                    }
                }

                if (count($args)) {
                    $routeUri .= '?' . http_build_query($args);
                }
            }

            $pattern = '/(\/\([a-zA-Z0-9]+\:[any|num|string|uri]+\))/';
            $routeUri = preg_replace($pattern, '', $routeUri);
        }

        if ($languageCode) {
            $routeUri = '/' . $languageCode . $routeUri;
        } elseif (count($this->config()->get('languages')) > 1) {
            $routeUri = '/' . $this->translator()->getCurrentLanguageCode() . $routeUri;
        }

        return $this->config()->getUrl($routeUri);
    }

    /**
     * Get route uri matches.
     *
     * @param string $routeUri
     *
     * @return array
     */
    protected function getRouteUriArgs($routeUri)
    {
        preg_match_all($this->routeUriRegexPattern, $routeUri, $matches);

        return $matches;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->app()->get('request');
    }
}
