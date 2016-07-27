<?php

namespace Neoflow\CMS\Views\Backend;

use \Neoflow\CMS\Views\BackendView;
use \Neoflow\Framework\Common\Container;

class NavigationView extends BackendView
{

    /**
     * @var Container
     */
    protected $cookies;

    public function __construct()
    {
        $this->cookies = $this->app()->get('request')->getCookies();
        parent::__construct();
    }
}
