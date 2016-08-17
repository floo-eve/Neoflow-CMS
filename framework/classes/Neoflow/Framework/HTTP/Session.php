<?php

namespace Neoflow\Framework\HTTP;

class Session
{

    /**
     * App trait
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var string
     */
    protected $sessionKey = '_session';

    /**
     * @var string
     */
    protected $flashKey = '_flash';

    /**
     * @var string
     */
    protected $flashDataNew = array();

    /**
     * @var bool
     */
    protected $reflash = false;

    /**
     * Start session.
     *
     * @return bool
     */
    public function start()
    {
        $this->logger()->info('Start session');

        $sessionConfig = $this->config()->get('session');
        $sessionName = $sessionConfig->get('name');
        if ($sessionName) {
            session_name($sessionName);
        }

        $sessionLifetime = $sessionConfig->get('lifetime');
        if (is_int($sessionLifetime)) {
            session_set_cookie_params($sessionLifetime);
        }

        $result = session_start();

        // Initialize session
        if (!isset($_SESSION[$this->sessionKey]) || !is_array($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = array();
        }

        // Initialize session flash
        if (!isset($_SESSION[$this->flashKey]) || !is_array($_SESSION[$this->flashKey])) {
            $_SESSION[$this->flashKey] = array();
        }

        register_shutdown_function(function () {
            $_SESSION[$this->flashKey] = $this->flashDataNew;
        });

        return $result;
    }

    /**
     * Destroy session.
     *
     * @return bool
     */
    public function destroy()
    {
        return session_destroy();
    }

    /**
     * Restart session.
     *
     * @return Session
     */
    public function restart()
    {
        if ($this->destroy()) {
            $this->start();
        }

        return $this;
    }

    /**
     * Keep flash data for the next request
     *
     * @return Session
     */
    public function reflash()
    {
        $this->flashDataNew = array_merge($_SESSION[$this->flashKey], $this->flashDataNew);
    }

    /**
     * Get session value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (isset($_SESSION[$this->sessionKey][$key])) {
            return $_SESSION[$this->sessionKey][$key];
        }

        return;
    }

    /**
     * Check wether session value exist.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return isset($_SESSION[$this->sessionKey][$key]);
    }

    /**
     * Delete session value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        if (isset($_SESSION[$this->sessionKey][$key])) {
            unset($_SESSION[$this->sessionKey][$key]);

            return true;
        }

        return false;
    }

    /**
     * Check wether session value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($_SESSION[$this->sessionKey][$key]);
    }

    /**
     * Set session value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Session
     */
    public function set($key, $value)
    {
        $_SESSION[$this->sessionKey][$key] = $value;

        return $this;
    }

    /**
     * Get session flash value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getFlash($key)
    {
        if ($this->hasFlash($key)) {
            return $_SESSION[$this->flashKey][$key];
        }

        return;
    }

    /**
     * Check wether flash value exists
     *
     * @param string $key
     * @return bool
     */
    public function hasFlash($key)
    {
        return isset($_SESSION[$this->flashKey][$key]);
    }

    /**
     * Delete new session flash value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function deleteFlash($key)
    {
        if (isset($this->flashDataNew[$key])) {
            unset($this->flashDataNew[$key]);

            return true;
        }

        return false;
    }

    /**
     * Set new session flash value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Session
     */
    public function setFlash($key, $value)
    {
        $this->flashDataNew[$key] = $value;

        return $this;
    }
}
