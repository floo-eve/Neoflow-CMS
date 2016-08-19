<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\Framework\Support\Validation\ValidationException;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\CMS\Support\Alert\DangerAlert;
use Neoflow\CMS\Support\Alert\SuccessAlert;

class SectionController extends BackendController
{

    /**
     * @var SectionModel
     */
    protected $section;

    /**
     * @var PageModel
     */
    protected $page;

    /**
     * @var ModuleModel
     */
    protected $module;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->view
            ->setSubtitle('Content')
            ->setTitle('Pages');
    }

    /**
     * Reorder sections action.

     *

     * @param array $args

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

     * @return Response
     */
    public function deleteAction($args)
    {

        // Get section by id

        $section = $this->getSectionById($args['id']);

        // Delete section

        if ($section->delete()) {
            $this->setSuccessAlert(translate('{0} successful deleted', array('Section')));
        } else {
            $this->setDangerAlert(translate('Delete failed'));
        }

        return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
    }

    /**
     * Activate section action.

     *

     * @param array $args

     * @return Response
     */
    public function activateAction($args)
    {

        // Get section by id

        $section = $this->getSectionById($args['id']);

        // Set state

        $section->is_active = !$section->is_active;

        // Save section

        if ($section->save()) {
            if ($section->is_active) {
                $this->setSuccessAlert(translate('Section successful activated'));
            } else {
                $this->setSuccessAlert(translate('Section successful disabled'));
            }
        }

        return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
    }

    /**
     * Get section by id.

     *

     * @param int $id

     * @return SectionModel

     * @throws Exception
     */
    protected function getSectionById($id)
    {

        // Get page by id

        $section = SectionModel::findById($id);

        if ($section) {
            return $section;
        }

        throw new Exception('Section not found');
    }
}
