<?php

namespace Neoflow\CMS;

use \ErrorException;
use \Exception;
use \InvalidArgumentException;
use \Neoflow\CMS\Handler\Config;
use \Neoflow\CMS\Handler\Router;
use \Neoflow\CMS\Handler\Translator;
use \Neoflow\CMS\Model\SettingModel;
use \Neoflow\CMS\Model\ModuleModel;

class App extends \Neoflow\Framework\App {

    /**
     * Initialize app.
     *
     * @param string $path
     */
    protected function initialize($path) {
        parent::initialize($path);

        $modules = ModuleModel::findAll();
        $this->set('modules', $modules);

        // Register CMS settings
        $settings = SettingModel::findById(1);

        if ($settings) {
            $settings->setReadOnly();
            $this->set('settings', $settings);
        } else {
            throw new Exception('Settings not found');
        }
    }

    /**
     * Create and set config.
     *
     * @param string $path Absolute path to config file
     *
     * @throws InvalidArgumentException
     */
    protected function setConfig($path) {
        $configFilePath = $path . '/config.php';

        if (!is_file($configFilePath)) {
            throw new InvalidArgumentException('Config file not found: ' . $configFilePath);
        }

        $configData = include $configFilePath;
        $config = new Config($configData, false, true);
        $config->set('path', $path);

        $this->set('config', $config);
    }

    /**
     * Create and set router.
     */
    protected function setRouter() {
        $this->set('router', new Router($this));
    }

    /**
     * Create and set translator.
     */
    protected function setTranslator() {
        $this->set('translator', new Translator($this));
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
    public function errorHandler($errno, $errstr, $errfile, $errline) {
        $ex = new ErrorException($errstr, 0, $errno, $errfile, $errline);
        parent::exceptionHandler($ex);
    }

    /**
     * Exception handler.
     *
     * @param Exception $ex
     */
    public function exceptionHandler($ex) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $this->get('logger')->logException($ex);

        try {
            $router = $this->get('router');
            $errorRoute = $router->getRouteByKey('frontend_error');
            $router->route($errorRoute, array($ex))->send();
            exit;
        } catch (Exception $ex) {
            parent::exceptionHandler($ex);
        }
    }

}
