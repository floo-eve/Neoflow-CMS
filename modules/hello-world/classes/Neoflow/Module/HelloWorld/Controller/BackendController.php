<?php

namespace Neoflow\Module\HelloWorld\Controller;

use \Neoflow\CMS\Controller\Backend\PageController;
use \Neoflow\Framework\HTTP\Responsing\Response;

class BackendController extends PageController
{

    /**
     * Index action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function indexAction($args)
    {
        $this->view->setTitle('Dashboard');

        return $this->render('module/helloworld/index');
    }
}
