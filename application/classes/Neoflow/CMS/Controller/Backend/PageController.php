<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Views\Backend\PageView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;

class PageController extends BackendController
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
     * Index page action.
     *
     * @param array $args
     *
     * @return Response|RediretResponse
     */
    public function indexAction($args)
    {

        // Get all languages
        $languages = LanguageModel::findAllByColumn('is_active', true);

        // Get active language
        $language_id = $this->request()->getGet('language_id');
        if (!$language_id) {
            if ($this->session()->has('language_id')) {
                $language_id = $this->session()->get('language_id');
            } else {
                $language_id = $languages[0]->id();
                $this->session()->reflash();

                return $this->redirectToRoute('page_index', array('language_id' => $language_id));
            }
        }
        $this->session()->set('language_id', $language_id);
        $activeLanguage = LanguageModel::findById($language_id);

        // Get navitems
        $navitems = NavitemModel::repo()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $activeLanguage->id())
            ->where('navigation_id', '=', 1)
            ->orderByAsc('position')
            ->fetchAll();

        // Get modules
        $modules = ModuleModel::findAll();

        return $this->render('backend/page/index', array(
                'languages' => $languages,
                'activeLanguage' => $activeLanguage,
                'navitems' => $navitems,
                'modules' => $modules,
        ));
    }

    /**
     * Create page action.
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

            // Create page
            $page = PageModel::create(array(
                    'title' => $postData->get('title'),
                    'language_id' => $postData->get('language_id'),
                    'is_active' => $postData->get('is_active'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id'),
                    'module_id' => $postData->get('module_id'),
            ));

            // Validate and save page
            if ($page && $page->validate() && $page->save()) {
                $this->setSuccessAlert(translate('Successful created'));
            } else {
                throw new Exception('Create user failed');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Edit page action.
     *
     * @param array $args
     *
     * @return Response
     *
     * @throws Exception
     */
    public function editAction($args)
    {
        // Get page or data if validation has failed
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $page = PageModel::update($data, $data['page_id']);
        } else {
            $page = PageModel::findById($args['id']);
            if (!$page) {
                throw new Exception('Page not found (ID: ' . $args['id'] . ')');
            }
        }

        // Get navitems
        $navitems = NavitemModel::repo()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $page->language_id)
            ->where('navigation_id', '=', 1)
            ->orderByAsc('position')
            ->fetchAll();

        // Get navitem of page
        $navitem = $page->navitems()
            ->where('navigation_id', '=', 1)
            ->fetch();

        // Set back url
        $this->view->setBackRoute('page_index', array('language_id' => $page->language_id));

        return $this->render('backend/page/edit', array(
                'page' => $page,
                'navitem' => $navitem,
                'navitems' => $navitems,
        ));
    }

    /**
     * Delete page action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function deleteAction($args)
    {
        // Delete user
        $result = PageModel::deleteById($args['id']);
        if ($result) {
            return $this
                    ->setSuccessAlert(translate('Successful deleted'))
                    ->redirectToRoute('page_index');
        }
        throw new Exception('Delete page failed (ID: ' . $args['id'] . ')');
    }

    /**
     * Update page action.
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
            $page = PageModel::update(array(
                    'title' => $postData->get('title'),
                    'is_active' => $postData->get('is_active'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id'),
                    'is_visible' => $postData->get('is_visible'),
                    'keywords' => $postData->get('keywords'),
                    'description' => $postData->get('description'),
                    ), $postData->get('page_id'));

            // Validate and save page
            if ($page && $page->validate() && $page->save()) {
                $this->setSuccessAlert(translate('Successful updated'));
            } else {
                throw new Exception('Update page failed (ID: ' . $postData->get('page_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('page_edit', array('id' => $page->id()));
    }

    /**
     * Toggle page activation action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function toggleActivationAction($args)
    {
        // Get page and toggle activation
        $page = PageModel::findById($args['id']);
        if ($page && $page->toggleActivation() && $page->save()) {
            if ($page->is_active) {
                $this->setSuccessAlert(translate('Successful enabled'));
            } else {
                $this->setSuccessAlert(translate('Successful disabled'));
            }

            return $this->redirectToRoute('page_index');
        }
        throw new Exception('Page not found or toggle activation failed (ID: ' . $args['id'] . ')');
    }

    /**
     * Initialize view.
     */
    protected function initView()
    {
        $this->view = new PageView();
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
