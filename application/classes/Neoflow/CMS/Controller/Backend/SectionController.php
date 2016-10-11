<?php

namespace Neoflow\CMS\Controller\Backend;

use InvalidArgumentException;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Core\AbstractView;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\CMS\Views\Backend\SectionView;
use Neoflow\Framework\Core\AbstractView as AbstractView2;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;
use function has_permission;
use function is_json;
use function translate;

class SectionController extends BackendController {

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
     *
     * @param AbstractView $view
     */
    public function __construct() {
        parent::__construct();

        $this->view
                ->setSubtitle('Content')
                ->setTitle('Pages');

        $section_id = $this->request()->getGet('section_id');
        if (!$section_id) {
            $section_id = $this->request()->getPost('section_id');
        }

        // Get section, module and page
        $this->section = SectionModel::findById($section_id);

        // Set back url
        $this->view->setBackRoute('section_index', array('id' => $this->section->page_id));
        $this->view->set('section_id', $this->section->id());
    }

    /**
     * Index action.
     *
     * @param array $args
     *
     * @return Response
     *
     * @throws Exception
     */
    public function indexAction($args) {
        // Get page by id
        $page = PageModel::findById($args['id']);
        if (!$page) {
            throw new Exception('Page not found (ID: ' . $args['id'] . ')');
        }

        // Get sections
        $sections = $page->sections()
                ->orderByAsc('position')
                ->fetchAll();

        // Set back url
        $this->view->setBackRoute('page_index', array('language_id' => $page->language_id));

        return $this->render('backend/section/index', array(
                    'page' => $page,
                    'modules' => ModuleModel::findAll(),
                    'sections' => $sections,
        ));
    }

    /**
     * Reorder sections action.
     *
     * @param array $args
     *
     * @return JsonResponse
     */
    public function reorderAction($args) {
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
     * Update section action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function updateAction($args) {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Update section
            $section = SectionModel::update(array(
                        'is_active' => $postData->get('is_active'),
                            ), $postData->get('section_id'));

            // Validate and save section
            if ($section && $section->validate() && $section->save()) {
                $this->setSuccessAlert(translate('Successful updated'));
            } else {
                throw new Exception('Section not found or update failed (ID: ' . $postData->get('section_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        // Module of section
        $module = $section->module()->fetch();

        return $this->redirectToRoute($module->route, array('section_id' => $section->id()));
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
    public function createAction($args) {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Create section
            $section = SectionModel::create(array(
                        'page_id' => $postData->get('page_id'),
                        'module_id' => $postData->get('module_id'),
                        'is_active' => $postData->get('is_active'),
                        'block' => 0,
            ));

            // Validate and save section
            if ($section->validate() && $section->save()) {
                $this->setSuccessAlert(translate('successful created'));
            } else {
                throw new \Exception('Create section failed');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('section_index', array('id' => $section->page_id));
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
    public function deleteAction($args) {
        // Get and delete section
        $section = SectionModel::findById($args['id']);
        if ($section && $section->delete()) {
            return $this
                            ->setSuccessAlert(translate('Successful deleted'))
                            ->redirectToRoute('section_index', array('id' => $section->page_id));
        }
        throw new Exception('Delete section failed (ID: ' . $args['id'] . ')');
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
    public function toggleActivationAction($args) {
        // Get section and toggle activation
        $section = SectionModel::findById($args['id']);
        if ($section && $section->toggleActivation() && $section->save()) {
            if ($section->is_active) {
                $this->setSuccessAlert(translate('Successful enabled'));
            } else {
                $this->setSuccessAlert(translate('Successful disabled'));
            }

            return $this->redirectToRoute('section_index', array('id' => $section->page_id));
        }
        throw new Exception('Section not found or toggle activation failed (ID: ' . $args['id'] . ')');
    }

    /**
     * Edit section action.
     *
     * @param array $args
     *
     * @return Response
     *
     * @throws Exception
     */
    public function editAction($args) {
        // Get section or data if validation has failed
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $this->section = SectionModel::update($data, $data['section_id']);
        } else {
            $this->section = SectionModel::findById($args['id']);
            if (!$this->section) {
                throw new Exception('Section not found (ID: ' . $args['id'] . ')');
            }
        }

        // Set back url
        $this->view->setBackRoute('section_index', array('id' => $this->section->page_id));

        return $this->render('backend/section/edit', array(
                    'section' => $this->section,
                    'page' => $this->section->page()->fetch(),
                    'module' => $this->section->module()->fetch(),
        ));
    }

    protected function render($viewFile, array $parameters = array(), Response $response = null) {

        $module = $this->section->module()->fetch();
        $page = $this->section->page()->fetch();

        $parameters = array_merge(array(
            'section' => $this->section,
            'page' => $page,
            'module' => $module), $parameters);

        if (!$this->view->isCurrentRoute('section*')) {
            $this->view->startBlock('module');
            echo $this->view->renderView($viewFile, $parameters);
            $this->view->stopBlock();
            $viewFile = 'backend/section/edit';
        } elseif ($this->view->isCurrentRoute('section_edit')) {
            $view = new SectionView();
            $view->set('section_id', $this->section->id());
            $this->router()->routeByKey($module->backend_route, array('section_id' => $this->section->id()), $view);
            $this->view->addContentToBlock('module', $view->getBlock(0));
        }
        return parent::render($viewFile, $parameters, $response);
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission() {
        return has_permission('manage_pages');
    }

    /**
     * Initialize view
     */
    protected function initView() {
        $this->view = new SectionView();
    }

}
