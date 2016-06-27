<?php

namespace Lahaina\CMS\Views;

class FrontendView extends \Lahaina\CMS\Core\AbstractView
{

    /**
     * Set theme
     */
    protected function setTheme()
    {
        $this->theme = $this->app()
            ->get('setting')
            ->theme()
            ->fetch();
    }
}
