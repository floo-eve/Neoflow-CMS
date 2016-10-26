<?php

namespace Neoflow\Framework\HTTP;

use \Exception;
use \Neoflow\Framework\Common\Container;
use \Neoflow\Framework\Handler\Config;

class Request {

    /**
     * App trait
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var bool
     */
    protected $hasFiles = false;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->data = new Container();

        $this->data->set('cookies', new Container($_COOKIE, true));
        $this->data->set('get', new Container($_GET, true, true));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->data->set('post', new Container($_POST, true, true));

            if (isset($_FILES)) {
                $this->hasFiles = true;

                // ReArray files data
                $files = array();
                foreach ($_FILES as $key => $file) {
                    if (is_array($file['name'])) {
                        $files[$key] = normalize_post_files($file);
                    } else {
                        $files[$key] = $file;
                    }
                }

                $this->data->set('files', new Container($files, true));
            } else {
                $this->data->set('files', new Container(array(), true));
            }
        } else {
            $this->data->set('post', new Container(array(), true, true));
        }
    }

    /**
     * Check method request.
     *
     * @param array|string $method e.g. GET, POST, PUT, HEAD
     *
     * @return bool
     */
    public function isMethod($method) {
        if (is_string($method)) {
            $method = array($method);
        }

        return in_array($_SERVER['REQUEST_METHOD'], array_map('strtoupper', $method));
    }

    /**
     * Get language code from HTTP header.
     *
     * @return string
     */
    public function getHttpLanguage() {
        return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    /**
     * Check wether request as files.
     *
     * @return bool
     */
    public function hasFiles() {
        return $this->hasFiles;
    }

    /**
     * Get language code from uri
     *
     * @return bool|string
     */
    public function getUriLanguage() {
        $requestUri = $this->getUri(true);

        if (preg_match('/\/([a-z]{2})(\/|\?|$)/', substr($requestUri, 0, 4), $languageMatches)) {
            return $languageMatches[1];
        }

        return false;
    }

    /**
     * Get request data.
     *
     * @param string $key
     *
     * @return Container
     *
     * @throws Exception
     */
    protected function getData($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        throw new Exception('Request data not found: ' . $key);
    }

    /**
     * Get post data.
     *
     * @return Container
     */
    public function getPostData() {
        return $this->getData('post');
    }

    /**
     * Get post value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getPost($key) {
        return $this->getPostData()->get($key);
    }

    /**
     * Get cookie value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getCookie($key) {
        return $this->getData('cookies')->get($key);
    }

    /**
     * Get cookies.
     *
     * @return Container
     */
    public function getCookies() {
        return $this->getData('cookies');
    }

    /**
     * Get get data.
     *
     * @return Container
     */
    public function getGetData() {
        return $this->getData('get');
    }

    /**
     * Get get value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getGet($key) {
        return $this->getGetData()->get($key);
    }

    /**
     * Get file data.
     *
     * @return Container
     */
    public function getFileData() {
        return $this->getData('files');
    }

    /**
     * Get file value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getFile($key) {
        return $this->getFileData()->get($key);
    }

    /**
     * Get uri.
     *
     * @param bool $withLanguageCode
     *
     * @return string
     */
    public function getUri($withLanguageCode = false) {
        $url = $this->config()->getUrl();
        $urlPath = parse_url($url, PHP_URL_PATH);
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestUri = str_replace(array($urlPath, '//'), array('', '/'), $requestUri);

        // Check wether language code exists and remove it
        if (!$withLanguageCode && $this->getUriLanguage()) {
            $requestUri = preg_replace('/^(\/[a-z]{2})(\/|\?|$)/', '/', $requestUri);
        }

        return $requestUri;
    }

}
