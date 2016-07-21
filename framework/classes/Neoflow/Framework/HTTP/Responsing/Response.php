<?php

namespace Neoflow\Framework\HTTP\Responsing;

use DateTime;
use InvalidArgumentException;

class Response
{

    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var bool
     */
    protected $isSent = false;

    /**
     * Set cookie value.
     *
     * @param string $key
     * @param string $value
     * @param string $time
     * @param mixed  $path
     * @param mixed  $domain
     * @param bool   $secure
     *
     * @return bool
     */
    public function setCookie($key, $value = '', $time = '+24 hour', $path = false, $domain = false, $secure = false)
    {
        // Create a date
        $date = new DateTime();
        // Modify it (+1hours; +1days; +20years; -2days etc)
        $date->modify($time);
        // Set cookie
        return setcookie($key, $value, $date->getTimestamp(), $path, $domain, $secure, true);
    }

    /**
     * Delete cookie value.
     *
     * @param string $key
     * @param mixed  $path
     * @param mixed  $domain
     * @param bool   $secure
     *
     * @return bool
     */
    public function deleteCookie($key, $path = false, $domain = false, $secure = false)
    {
        return $this->setCookie($key, '', '-1 hour', $path, $domain, $secure);
    }

    /**
     * Send HTTP header.
     */
    protected function sendHeader()
    {

        if (is_int($this->statusCode)) {
            http_response_code($this->statusCode);
        }
        foreach ($this->headers as $header) {
            if (!in_array($header, headers_list())) {
                header($header);
            }
        }
    }

    /**
     * Set header.
     *
     * @param string $header
     *
     * @return Response
     */
    public function setHeader($header)
    {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * Set HTTP status code.
     *
     * @param int $code
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function setStatusCode($code)
    {
        $statusCodes = [100, 101, 102, 200, 201, 202, 203, 204, 205, 206,
            207, 208, 226, 300, 301, 302, 303, 304, 305, 306, 307, 308, 400, 401,
            402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415,
            416, 417, 418, 420, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451,
            444, 449, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511];

        if (!in_array($code, $statusCodes)) {
            throw new InvalidArgumentException('Status code is not a valid HTTP code: ' . $code);
        }
        $this->statusCode = $code;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content.
     *
     * @param $content $content
     *
     * @return Response
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Send content as echo.
     */
    protected function sendContent()
    {
        echo $this->content;
    }

    /**
     * Send response.
     */
    public function send()
    {
        if (!$this->isSent) {
            $this->sendHeader();
            $this->sendContent();
            $this->isSent = true;
        }
    }

    /**
     * Check if.
     *
     * @return type
     */
    public function isSent()
    {
        return $this->isSent;
    }
}
