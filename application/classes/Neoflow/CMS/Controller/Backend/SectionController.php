<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
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
     * Reorder sections action.
     *
     * @param array $args
     *
     * @return JsonResponse
     */
    public function reorderAction($args)
    {
        // Get json request
        $json = file_get_contents('php://input');

        // Reorder and update navigation item
        $result = false;
        if (is_json($json)) {
            $result = $this
                ->service('section')
                ->updateOrder(json_decode($json, true));
        }

        return new JsonResponse(array('success' => $result));
    }

    /**
     * Create section action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function createAction($args)
    {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Create section
            $section = SectionModel::create(array(
                    'page_id' => $postData->get('page_id'),
                    'module_id' => $postData->get('module_id'),
                    'is_active' => $postData->get('is_active'),
                    'block' => 1,
            ));

            // Validate and save section
            if ($section->validate() && $section->save()) {
                $this->setSuccessAlert(translate('successful created'));
            } else {
                throw new Exception('Create section failed');
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
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function deleteAction($args)
    {
        // Get and delete section
        $section = SectionModel::findById($args['id']);
        if ($section && $section->delete()) {
            return $this
                    ->setSuccessAlert(translate('Successful deleted'))
                    ->redirectToRoute('page_sections', array('id' => $section->page_id));
        }
        throw new Exception('Delete section failed (ID: '.$args['id'].')');
    }

    /**
     * Toggle section activation action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function toggleActivationAction($args)
    {
        // Get section and toggle activity
        $section = SectionModel::findById($args['id']);
        if ($section && $section->toggleActivation() && $section->save()) {
            if ($section->is_visible) {
                $this->setSuccessAlert(translate('Successful activated'));
            } else {
                $this->setSuccessAlert(translate('Successful disabled'));
            }

            return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
        }
        throw new Exception('Section not found or toggle activation failed (ID: '.$args['id'].')');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission()
    {
        return has_permission('manage_pages');
    }
}
