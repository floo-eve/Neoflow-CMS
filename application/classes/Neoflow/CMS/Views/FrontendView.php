<?php

namespace Neoflow\CMS\Views;

class FrontendView extends \Neoflow\CMS\Core\AbstractView
{
    /**
     * Set theme.
     */
    protected function setTheme()
    {
        $this->theme = $this->app()
            ->get('setting')
            ->theme()
            ->fetch();
    }
}
