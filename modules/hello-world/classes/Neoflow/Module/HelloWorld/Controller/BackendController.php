<?php

namespace Neoflow\Module\HelloWorld\Controller;

use \Neoflow\CMS\Controller\Backend\SectionController;
use \Neoflow\Framework\HTTP\Responsing\Response;

class BackendController extends SectionController
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
        return $this->render('module/helloworld/index');
    }
}
