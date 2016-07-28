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

        // Create mapper
        $this->sectionMapper = new SectionMapper();

        $section_id = $this->getRequest()->getGet('section_id');
        if (!$section_id) {
            $section_id = $this->getRequest()->getPost('section_id');
        }

        // Get section, module and page
        if ($section_id) {
            $this->section = $this->getSectionById($section_id);
            $this->module = $this->section->module()->fetch();
            $this->page = $this->section->page()->fetch();

            // Set back url
            $this->view->setBackRoute('page_sections', array('id' => $this->page->id()));
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

            if ($section->save()) {
                $this->setFlash('alert', new SuccessAlert('Section successful save'));
            }
        } catch (ValidationException $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getErrors()));
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
        // Get section by id
        $section = $this->getSectionById($args['id']);

        // Delete section
        if ($section->delete()) {
            $this->setFlash('alert', new SuccessAlert('Section successful deleted'));
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
        // Get section by id
        $section = $this->getSectionById($args['id']);

        // Set state
        $section->is_active = !$section->is_active;

        // Save section
        if ($section->save()) {
            if ($section->is_active) {
                $this->setFlash('alert', new SuccessAlert('Section successful activated'));
            } else {
                $this->setFlash('alert', new SuccessAlert('Section successful disabled'));
            }
        }

        return $this->redirectToRoute('page_sections', array('id' => $section->page_id));
    }

    protected function render($viewFile, array $parameters = array(), Response $response = null)
    {
        $this->view->startBlock('module');
        echo $this->view->renderView($viewFile, $parameters);
        $this->view->stopBlock();

        return parent::render('backend/section/index', array(), $response);
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
