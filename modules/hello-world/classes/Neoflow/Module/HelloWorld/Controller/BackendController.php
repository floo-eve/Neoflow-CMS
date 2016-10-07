<?php

namespace Neoflow\Module\HelloWorld\Controller;

use Neoflow\CMS\Controller\Backend\Module\AbstractPageController;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\HelloWorld\Model\MessageModel;

class BackendController extends AbstractPageController
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

    /**
     * Update action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Get page by id
            $message = \Neoflow\Module\HelloWorld\Model\MessageModel::update(array(
                    'message' => $postData->get('message')
                    ), $postData->get('message_id'));

            // Validate and save page
            if ($message && $message->save()) {
                $this->setSuccessAlert(translate('Successful updated'));
            } else {
                throw new Exception('Update message failed (ID: ' . $postData->get('message_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('mod_hello_world_index', array('section_id' => $message->section_id));
    }
}
