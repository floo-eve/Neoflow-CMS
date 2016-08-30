<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;

class SectionController extends BackendController
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Set title
        $this->view
            ->setSubtitle('Content')
            ->setTitle('Pages');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    public function checkPermission()
    {
        return has_permission('manage_pages');
    }

    /**
     * Reorder sections action.
     *
     * @param array $args
     *
     * @return JsonResponse
     */
    public function reorderAction($args)
    {

        // Get json data and update order of sections
        $json = file_get_contents('php://input');

        $result = false;

        if (is_json($json)) {
            $result = $this
                ->service('section')
                ->updateOrder(json_decode($json, true));
        }

        return new JsonResponse(array('success' => (bool) $result));
    }

    /**
     * Create new section action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function createAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            $section = SectionModel::create(array(
                    'page_id' => $postData->get('page_id'),
                    'module_id' => $postData->get('module_id'),
                    'is_active' => $postData->get('is_active'),
                    'block' => 1,
            ));

            if ($section->validate() && $section->save()) {
                $this->setSuccessAlert(translate('{0} successful saved', array('Section')));
            } else {
                $this->setDangerAlert(translate('Create failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
    }

    /**
     * Delete section action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function deleteAction($args)
    {

        // Get section
        $section = SectionModel::findById($args['id']);

        if ($section) {
            if ($section->delete()) {
                $this->setSuccessAlert(translate('{0} successful deleted', array('Section')));
            } else {
                $this->setDangerAlert(translate('Delete failed'));
            }

            return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
        }

        $this->setDangerAlert(translate('{0} not found', array('Section')));

        return $this->redirectToRoute('page_index');
    }

    /**
     * Activate section action.
     *
     * @param array $args
     *
     * @return RedirectResponse|Response
     */
    public function activateAction($args)
    {

        // Get section
        $section = SectionModel::findById($args['id']);

        if ($section) {
            $section
                ->toggleActivation()
                ->save();

            if ($section->is_active) {
                $this->setSuccessAlert(translate('Section successful activated'));
            } else {
                $this->setSuccessAlert(translate('Section successful disabled'));
            }

            return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
        }

        $this->setDangerAlert(translate('{0} not found', array('Section')));

        return $this->redirectToRoute('page_index');
    }
}
