<?php

namespace Neoflow\Module\HelloWorld\Controller;

use Neoflow\CMS\Controller\Backend\Module\AbstractBackendController;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\HelloWorld\Repository\MessageRepository;

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
        $messageRepository = new MessageRepository();

        $message = $messageRepository->where('section_id', '=', $this->section->id())->fetch();

        return $this->render('module/helloworld/index', array(
                'message' => $message,
        ));
    }
}
