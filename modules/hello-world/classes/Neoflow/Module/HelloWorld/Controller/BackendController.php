<?php

namespace Neoflow\Module\HelloWorld\Controller;

use Neoflow\CMS\Controller\Backend\Module\AbstractBackendController;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\HelloWorld\Model\MessageModel;

class BackendController extends AbstractBackendController
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
        $message = MessageModel::findByColumn('section_id', $this->section->id());

        return $this->render('module/helloworld/index', array(
                'message' => $message,
        ));
    }
}
