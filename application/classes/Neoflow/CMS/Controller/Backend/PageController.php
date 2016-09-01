<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Views\Backend\PageView;
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
     * Check permission.
     *
     * @return bool
     */
    public function checkPermission()
    {
        return has_permission('manage_pages');
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

        // Get page language id
        $language_id = $this->request()->getGet('language_id');

        if (!$language_id) {
            if ($this->session()->has('page_language_id')) {
                $language_id = $this->session()->get('page_language_id');
            } else {
                $language_id = $languages[0]->id();
                $this->session()->reflash();

                return $this->redirectToRoute('page_index', array('language_id' => $language_id));
            }
        }
        $this->session()->set('page_language_id', $language_id);

        // Get page language
        $pageLanguage = LanguageModel::findById($language_id);

        // Get navitems
        $navitems = NavitemModel::repo()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $pageLanguage->id())
            ->where('navigation_id', '=', 1)
            ->orderByAsc('position')
            ->fetchAll();

        // Get modules
        $modules = ModuleModel::findAll();

        return $this->render('backend/page/index', array(
                'languages' => $languages,
                'pageLanguage' => $pageLanguage,
                'navitems' => $navitems,
                'modules' => $modules,
        ));
    }

    /**
     * Create action.
     *
     * @param array $args
     *
     * @return Response
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

            if ($page->validate() && $page->save()) {
                $this->setSuccessAlert(translate('{0} successful created', array('Page')));
            } else {
                $this->setDangerAlert(translate('Create failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('page_index');
    }

    public function sectionsAction($args)
    {
        // Get page by id
        $page = PageModel::findById($args['id']);
        if (!$page) {
            $this->setDangerAlert(translate('{0} not found', array('User')));

            return $this->redirectToRoute('page_index');
        }

        // Get sections
        $sections = $page->sections()
            ->orderByAsc('position')
            ->fetchAll();

        // Get modules
        $modules = ModuleModel::findAll();

        // Set back url
        $this->view->setBackRoute('page_index', array('language_id' => $page->language_id));

        return $this->render('backend/page/sections', array(
                'page' => $page,
                'modules' => $modules,
                'sections' => $sections,
        ));
    }

    public function editAction($args)
    {

        // Get page data if validation has failed)
        if ($this->service('validation')->hasError()) {
            $pageData = $this->service('validation')->getData();

            $page = new PageModel($pageData);
        } else {

            // Get page by id
            $page = PageModel::findById($args['id']);
            if (!$page) {
                $this->setDangerAlert(translate('{0} not found', array('User')));

                return $this->redirectToRoute('page_index');
            }
        }

        // Get navitems
        $navitems = NavitemModel::repo()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $page->language_id)
            ->where('navigation_id', '=', 1)
            ->orderByAsc('position')
            ->fetchAll();

        // Get parent navitem
        $pageNavitem = $page->navitems()
            ->where('navigation_id', '=', 1)
            ->fetch();

        $parentNavitem = $pageNavitem->parentNavitem()->fetch();

        // Set back url
        $this->view->setBackRoute('page_index', array('language_id' => $page->language_id));

        return $this->render('backend/page/edit', array(
                'page' => $page,
                'pageNavitem' => $pageNavitem,
                'navitems' => $navitems,
                'selectedNavitemId' => ($parentNavitem ? $parentNavitem->id() : false),
        ));
    }

    /**
     * Delete page action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function deleteAction($args)
    {
        // Delete page
        $result = PageModel::deleteById($args['id']);

        if ($result) {
            $this->setSuccessAlert(translate('{0} successful deleted', array('Page')));
        } else {
            $this->setDangerAlert(translate('Delete failed'));
        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Update page action.
     *
     * @param array $args
     *
     * @return Response
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
                    'is_hidden' => $postData->get('is_hidden'),
                    'keywords' => $postData->get('keywords'),
                    'description' => $postData->get('description'),
                    ), $postData->get('page_id'));

            // Save page and navitem
            if ($page->validate() && $page->save()) {
                $this->setSuccessAlert(translate('{0} successful updated', array('Page')));
            } else {
                $this->setDangerAlert(translate('Update failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('page_edit', array('id' => $page->id()));
    }

    /**
     * Activate page action.

     *

     * @param array $args
     *
     * @return Response
     */
    public function activateAction($args)
    {

        // Get page
        $page = PageModel::findById($args['id']);

        if ($page) {
            $page
                ->toggleActivation()
                ->save();

            if ($page->is_active) {
                $this->setSuccessAlert(translate('Page successful activated'));
            } else {
                $this->setSuccessAlert(translate('Page successful disabled'));
            }
        } else {
            $this->setDangerAlert(translate('{0} not found', array('Page')));
        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Set view.
     */
    protected function setView()
    {
        $this->view = new PageView();
    }
}
