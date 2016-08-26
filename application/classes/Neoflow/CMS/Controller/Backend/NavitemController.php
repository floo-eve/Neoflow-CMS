<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\Support\Validation\ValidationException;
use function is_json;
use function translate;

class NavitemController extends BackendController
{

    /**
     * Reorder navitems action.
     *
     * @param array $args
     *
     * @return JsonResponse
     */
    public function reorderAction($args)
    {
        $json = file_get_contents('php://input');

        $result = false;

        if (is_json($json)) {
            $result = $this
                ->service('navitem')
                ->updateOrder(json_decode($json, true));
        }

        return new JsonResponse(array('success' => $result));
    }

    /**
     * Create navitem action
     *
     * @param array $args
     * @return Response
     */
    public function createAction($args)
    {
        try {

// Get post data
            $postData = $this->getRequest()->getPostData();

// Create navigation
            $navitem = NavitemModel::create(array(
                    'title' => $postData->get('title'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id') ? : null,
                    'navigation_id' => $postData->get('navigation_id'),
                    'language_id' => $postData->get('language_id'),
                    'page_id' => $postData->get('page_id'),
                    'is_visible' => $postData->get('is_visible'),
            ));

            if ($navitem->navigation_id !== 1 && $navitem->validate() && $navitem->save()) {
                $this->setSuccessAlert(translate('{0} successful created', array('Navigation item')));
            } else {
                $this->setDangerAlert(translate('Create failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('navigation_navitems', array('id' => $navitem->navigation_id));
    }

    /**
     * Toggle visibility action.
     *
     * @param array $args
     *
     * @return RedirectResponse|Response
     */
    public function toggleVisiblityAction($args)
    {

        // Get section
        $navitem = NavitemModel::findById($args['id']);

        if ($navitem) {
            $navitem
                ->toggleVisibility()
                ->save();

            if ($navitem->is_visible) {
                $this->setSuccessAlert(translate('Navigation item successful made visible'));
            } else {
                $this->setSuccessAlert(translate('Navigation item successful hidden'));
            }

            return $this->redirectToRoute('navigation_navitems', array('id' => $navitem->navigation_id));
        }

        $this->setDangerAlert(translate('{0} not found', array('Navigation item')));

        return $this->redirectToRoute('navigation_index');
    }

    /**
     * Delete navitem action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function deleteAction($args)
    {

        // Get navitem
        $navitem = NavitemModel::findById($args['id']);

        if ($navitem) {
            if ($navitem->delete()) {
                $this->setSuccessAlert(translate('{0} successful deleted', array('Navigation item')));
            } else {
                $this->setDangerAlert(translate('Delete failed'));
            }

            return $this->redirectToRoute('navigation_navitems', array('id' => $navitem->navigation_id));
        }

        $this->setDangerAlert(translate('{0} not found', array('Navigation item')));

        return $this->redirectToRoute('navigation_index');
    }
}
