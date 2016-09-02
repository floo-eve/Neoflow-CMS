<?php

namespace Neoflow\CMS\Views;

use Neoflow\CMS\Core\AbstractView;
use Neoflow\CMS\Model\UserModel;
use function generate_url;
use function translate;

class FrontendView extends AbstractView
{

    protected function initTheme()
    {
        $this->theme = $this->app()
            ->get('settings')
            ->theme()
            ->fetch();
    }
}
