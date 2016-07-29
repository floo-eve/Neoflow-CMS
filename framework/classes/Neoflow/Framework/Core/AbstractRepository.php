<?php

namespace Neoflow\Framework\Core;

use \Neoflow\Framework\Persistence\ORM;

abstract class AbstractRepository extends ORM
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->forModel($this->modelClassName);
    }
}
