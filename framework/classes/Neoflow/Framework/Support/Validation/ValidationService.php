<?php

namespace Neoflow\Framework\Support\Validation;

use Neoflow\Framework\Core\AbstractService;
use Neoflow\Framework\HTTP\Session;

class ValidationService extends AbstractService
{

    /**
     * Check wether validation error exists
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasError($key = '')
    {
        $validationErrors = $this->session()->getFlash('validationErrors');
        return (is_array($validationErrors) && ($key === '' || isset($validationErrors[$key])));
    }

    /**
     * Get validated data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->session()->getFlash('validationData') ? : array();
    }
}
