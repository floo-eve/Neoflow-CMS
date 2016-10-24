<?php

namespace Neoflow\Module\HelloWorld\Controller;

use Neoflow\CMS\Controller\FrontendController;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\HelloWorld\Model\MessageModel;

class FrontendController extends FrontendController
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
        $message = MessageModel::findByColumn('section_id', $this->view->get('section_id'));

        return $this->render('module/helloworld/frontend', array(
                'message' => $message
        ));
    }
}
