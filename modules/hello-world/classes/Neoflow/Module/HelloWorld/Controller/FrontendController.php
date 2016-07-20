<?php

namespace Neoflow\Module\HelloWorld\Controller;

use \Neoflow\Framework\HTTP\Responsing\Response;

class FrontendController extends \Neoflow\CMS\Controller\FrontendController
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
