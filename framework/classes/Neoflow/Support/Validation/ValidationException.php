<?php

namespace Neoflow\Support\Validation;

use Exception;

class ValidationException extends Exception
{

    protected $errors = array();

    public function __construct($message, array $errors = array())
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
