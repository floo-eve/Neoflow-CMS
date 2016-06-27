<?php

namespace Lahaina\Framework;

use \ErrorException;
use \Exception;
use \InvalidArgumentException;
use \Lahaina\Framework\Handler\Config;
use \Lahaina\Framework\Handler\Logging\Logger;
use \Lahaina\Framework\Handler\Router;
use \Lahaina\Framework\Handler\Translator;
use \Lahaina\Framework\HTTP\Request;
use \Lahaina\Framework\HTTP\Responsing\Response;
use \Lahaina\Framework\HTTP\Session;
use \Lahaina\Framework\Persistence\Caching\ApcCache;
use \Lahaina\Framework\Persistence\Caching\ApcuCache;
use \Lahaina\Framework\Persistence\Caching\DisabledCache;
use \Lahaina\Framework\Persistence\Caching\FileCache;
use \Lahaina\Framework\Persistence\Database;

class App
{

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $readonly = ['config', 'logger', 'cache',
        'database', 'pdo', 'db', 'router',
        'session', 'request', 'router', 'translator',];

    /**
     * @var App
     */
    protected static $instance;

    /**
     * @var float
     */
    protected $startTime;

    /**
     * @var bool
     */
    protected $isPublished = false;

    /**
     * Constructor.
     *
     * @param Config
     */
    public function __construct($path = __DIR__)
    {
        // Set start time in seconds
        $this->startTime = microtime(true);
        // Safe current app instance
        self::$instance = $this;
        // Initialize application
        $this->initialize($path);
    }

    /**
     * Get execution time in seconds.
     *
     * @return float
     */
    public function getExecutionTime()
    {
        return microtime(true) - $this->startTime;
    }

    /**
     * Initialize app.
     *
     * @param string $path
     */
    protected function initialize($path)
    {
        $this->setConfig($path);
        $this->setLogger();
        $this->setError();
        $this->setCache();
        $this->setDatabase();
        $this->setSession();
        $this->setRequest();
        $this->setRouter();
        $this->setTranslator();

        $this->get('logger')->info('Application initialized');
    }

    /**
     * Set value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return App
     *
     * @throws InvalidArgumentException
     */
    public function set($key, $value)
    {
        if ($this->has($key) && in_array($key, $this->readonly)) {
            throw new InvalidArgumentException('Value is readonly and already set with this key: ' . $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get object.
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }
        throw new InvalidArgumentException('Cannot find object of current key: ' . $key);
    }

    /**
     * Check wether object exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Get app instance.
     *
     * @return App
     */
    public static function instance()
    {
        return self::$instance;
    }

    /**
     * Publish application, send response and end script.
     */
    public function publish()
    {
        $this->get('logger')->info('Publish application');
        if (!$this->has('response')) {
            $this->set('response', new Response());
        }

        if (!$this->get('response')->isSent()) {
            $this->get('response')->send();
        }

        $this->isPublished = true;
        $this->get('logger')->info('# Application published');
        exit;
    }

    /**
     * Execute application and get response.
     *
     * @param Router $router
     *
     * @return Response
     */
    public function execute()
    {
        $this->get('session')->start();

        $this->get('translator')
            ->identifyLanguage()
            ->loadTranslation();

        $response = $this->get('router')
            ->loadRoutes()
            ->startRouting();

        $this->set('response', $response);

        $this->get('logger')->info('Application executed');

        return $response;
    }

    /**
     * Create and set config.
     *
     * @param string $path Absolute path to config file
     */
    protected function setConfig($path)
    {
        $configFilePath = $path . '/config.php';

        if (!is_file($configFilePath)) {
            throw new Exception('Config file not found: ' . $configFilePath);
        }

        $configData = include $configFilePath;
        $config = new Config($configData, false, true);
        $config->set('path', $path);

        $this->set('config', $config);
    }

    /**
     * Create and set translator.
     */
    protected function setTranslator()
    {
        $this->set('translator', new Translator($this));
    }

    /**
     * Create and set cache instance.
     */
    protected function setCache()
    {
        // Get cache config
        $cacheConfig = $this->get('config')->get('cache');

        if ($cacheConfig === 'apcu' || ($cacheConfig === 'auto' && extension_loaded('apcu') && ini_get('apc.enabled'))) {
            $cache = new ApcuCache($this);
        } elseif ($cacheConfig === 'apc' || ($cacheConfig === 'auto' && extension_loaded('apc') && ini_get('apc.enabled'))) {
            $cache = new ApcCache($this);
        } elseif (in_array($cacheConfig, array('auto', 'file'))) {
            $cache = new FileCache($this);
        } else {
            $cache = new DisabledCache();
        }
        $this->set('cache', $cache);
    }

    /**
     * Create and set database.
     */
    protected function setDatabase()
    {
        // Get database config
        $databaseConfig = $this->get('config')->get('database');
        // Set DSN
        $dsn = 'mysql:host=' . $databaseConfig->get('host');
        $dsn .= ';dbname=' . $databaseConfig->get('dbname');
        $dsn .= ';charset=' . $databaseConfig->get('charset');
        // Set options
        $options = array(
            Database::ATTR_PERSISTENT => true,
            Database::ATTR_ERRMODE => Database::ERRMODE_EXCEPTION,
            Database::ATTR_STRINGIFY_FETCHES => false,);
        // Create PDO instance
        $database = new Database($dsn, $databaseConfig->get('username'), $databaseConfig->get('password'), $options);

        $this
            ->set('database', $database)
            ->set('db', $database)
            ->set('pdo', $database);
    }

    /**
     * Create and set registry.
     */
    protected function setSession()
    {
        $this->set('session', new Session());
    }

    /**
     * Create and set request.
     */
    protected function setRequest()
    {
        $this->set('request', new Request());
    }

    /**
     * Create and set router.
     */
    protected function setRouter()
    {
        $this->set('router', new Router());
    }

    /**
     * Create and set logger.
     */
    protected function setLogger()
    {
        $logger = new Logger();
        $this->set('logger', $logger);
    }

    /**
     * Set error.
     */
    protected function setError()
    {
        set_error_handler(array($this, 'errorHandler'), E_ALL);
        register_shutdown_function(array($this, 'shutdownFunction'));

        set_exception_handler(array($this, 'exceptionHandler'));
    }

    /**
     * Error handler.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @param array  $errcontext
     *
     * @throws ErrorException
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $ex = new ErrorException($errstr, 0, $errno, $errfile, $errline);
        $this->exceptionHandler($ex);
    }

    /**
     * Exception handler.
     *
     * @param \Exception $ex
     */
    public function exceptionHandler($ex)
    {
        while (ob_get_level() > 1) {
            ob_end_clean();
        }

        $this->get('logger')->logException($ex);

        $content = str_replace(array('[title]', '[message]', '[exception]', '[time]'), array('Fatal server error', $ex->getMessage(), get_class($ex), $this->get('translator')->formatDateTime(new \DateTime())), '<!DOCTYPE html>
                        <html>
                            <head>
                                <meta charset="UTF-8" />
                                <title>[title]</title>
                            </head>
                            <body>
                                <h1>[title]</h1>
                                <h2>[exception]: [message]</h2>
                                <p>[time]</p>
                            </body>
                        </html>');
        die($content);
    }

    /**
     * Shutdown function.
     *
     * @throws ErrorException
     */
    public function shutdownFunction()
    {
        $error = error_get_last();
        if ($error['type'] === E_ERROR) {
            $this->errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}