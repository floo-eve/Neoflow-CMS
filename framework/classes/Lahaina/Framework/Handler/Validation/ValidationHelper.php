<?php

namespace Lahaina\Framework\Handler\Validation;

use Lahaina\Framework\HTTP\Session;

class ValidationHelper
{

    /**
     * App trait.
     */
    use \Lahaina\Framework\AppTrait;

    /**
     * Check wether validation error exists
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasError($key = '')
    {
        $validationErrors = $this->getSession()->getFlash('validationErrors');
        return (is_array($validationErrors) && ($key === '' || isset($validationErrors[$key])));
    }

    /**
     * Get validated data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->getSession()->getFlash('validationData') ? : array();
    }

    /**
     * Get session.
     *
     * @return Session
     */
    protected function getSession()
    {
        return $this->app()->get('session');
    }
}
