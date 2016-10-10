<?php

namespace Neoflow\Module\HelloWorld\Controller;

use Exception;
use Neoflow\CMS\Controller\Backend\SectionController;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;
use Neoflow\Module\HelloWorld\Model\MessageModel;
use function translate;

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
        $message = MessageModel::findByColumn('section_id', $this->view->get('section_id'));

        return $this->render('module/helloworld/backend', array(
                'message' => $message,
        ));
    }

    /**
     * Index action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function blaAction($args)
    {
        $message = MessageModel::findByColumn('section_id', $this->view->get('section_id'));

        return $this->render('module/helloworld/bla', array(
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
            $message = MessageModel::update(array(
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

        return $this->redirectToRoute('section_edit', array('id' => $message->section_id));
    }
}
