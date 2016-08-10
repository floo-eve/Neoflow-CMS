<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;

class DashboardController extends BackendController
{
    public function indexAction($args)
    {
        $this->view->setTitle('Dashboard');

        return $this->render('backend/dashboard/index');
    }
}
