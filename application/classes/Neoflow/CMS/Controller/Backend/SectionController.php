<?php

namespace Neoflow\CMS\Controller\Backend;

use \Exception;
use \Neoflow\CMS\Controller\BackendController;
use \Neoflow\CMS\Mapper\ModuleMapper;
use \Neoflow\CMS\Mapper\SectionMapper;
use \Neoflow\CMS\Model\PageModel;
use \Neoflow\CMS\Model\SectionModel;
use \Neoflow\Framework\Handler\Validation\ValidationException;
use \Neoflow\Framework\HTTP\Responsing\JsonResponse;
use \Neoflow\Framework\HTTP\Responsing\Response;
use \Neoflow\Helper\Alert\DangerAlert;
use \Neoflow\Helper\Alert\SuccessAlert;
use function \is_json;

class SectionController extends BackendController
{

    /**
     * @var ModuleMapper
     */
    protected $moduleMapper;

    /**
     * @var SectionMapper
     */
    protected $sectionMapper;

    /**
     * @var SectionModel
     */
    protected $section;

    /**
     * @var PageModel
     */
    protected $page;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Create mapper
        $this->moduleMapper = new ModuleMapper();
        $this->sectionMapper = new SectionMapper();

        $section_id = $this->getRequest()->getGet('section_id');
        if (!$section_id) {
            $section_id = $this->getRequest()->getPost('section_id');
        }

        if ($section_id) {
            $this->section = $this->getSectionById($section_id);
            $this->page = $this->section->page()->fetch();
        }
    }

    public function reorderAction($args)
    {
        $json = file_get_contents('php://input');
        $result = false;
        if (is_json($json)) {
            $result = $this->sectionMapper->updateOrder(json_decode($json, true));
        }
        return new JsonResponse(array('success' => (bool) $result));
    }

    /**
     * New section action
     *
     * @param array $args
     * @return Response
     */
    public function createAction($args)
    {
        try {
            // Get post data
            $postData = $this->getRequest()->getPostData();

            $section = new SectionModel();
            $section->page_id = $postData->get('page_id');
            $section->module_id = $postData->get('module_id');
            $section->is_active = $postData->get('is_active');
            $section->block = 1;

            if ($section->validate() && $section->save()) {
                $this->setFlash('alert', new SuccessAlert('Section successful save'));
            } else {
                throw new \Exception('Transaction failed');
            }
        } catch (ValidationException $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getErrors()));
        } catch (Exception $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getMessage()));
        }
        return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
    }

    /**
     * Delete section action
     *
     * @param array $args
     * @return Response
     */
    public function deleteAction($args)
    {
        try {
            $section = $this->getSectionById($args['id']);

            if ($section->delete()) {
                $this->setFlash('alert', new SuccessAlert('Section successful deleted'));
            } else {
                throw new Exception('Transcation failed');
            }
        } catch (Exception $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getMessage()));
        }
        return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
    }

    /**
     * Activate section action
     *
     * @param array $args
     * @return Response
     */
    public function activateAction($args)
    {
        try {
            $section = $this->getSectionById($args['id']);

            $section->is_active = !$section->is_active;
            $section->save();

            if ($section->is_active) {
                $this->setFlash('alert', new SuccessAlert('Section successful activated'));
            } else {
                $this->setFlash('alert', new SuccessAlert('Section successful disabled'));
            }
        } catch (Exception $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getMessage()));
            return $this->redirectToRoute('page_index');
        }
        return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
    }

    protected function render($viewFile, array $parameters = array(), Response $response = null)
    {

        $this->view->startBlock('module');
        echo $this->view->renderView($viewFile, $parameters);
        $this->view->stopBlock();

        return parent::render('backend/section/index', array(
                'section' => $this->section,
                'page' => $this->page,
                ), $response);
    }

    protected function getSectionById($id)
    {
        // Get page by id
        $section = $this->sectionMapper->findById($id);

        if ($section) {
            return $section;
        }

        throw new Exception('Section not found');
    }
}
