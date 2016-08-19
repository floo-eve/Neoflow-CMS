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

        // Set titles
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

        // Get page language id
        $language_id = $this->getRequest()->getGet('language_id');

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
     * @param  array    $args
     * @return Response
     */
    public function createAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Create page
            $page = PageModel::create(array(
                    'title' => $postData->get('title'),
                    'language_id' => $postData->get('language_id'),
                    'is_active' => $postData->get('is_active'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id'),
                    'visibility' => $postData->get('visibility'),
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
        $page = $this->getPageById($args['id']);

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

    public function settingsAction($args)
    {

        // Get page data if validation has failed)
        if ($this->service('validation')->hasError()) {
            $pageData = $this->service('validation')->getData();

            $page = new PageModel($pageData);
        } else {

            // Get page by id
            $page = $this->getPageById($args['id']);
        }

        // Get navitems
        $navitems = NavitemModel::repo()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $page->language_id)
            ->where('navigation_id', '=', 1)
            ->orderByAsc('position')
            ->fetchAll();

        // Get parent navitem

        $navitem = $page->navitems()
            ->where('navigation_id', '=', 1)
            ->fetch();

        $parentNavitem = $navitem->parentNavitem()->fetch();

        // Set back url
        $this->view->setBackRoute('page_index', array('language_id' => $page->language_id));

        return $this->render('backend/page/settings', array(
                'page' => $page,
                'navitems' => $navitems,
                'selectedNavitemId' => ($parentNavitem ? $parentNavitem->id() : false),
                'disabledNavitemIds' => array($navitem->id()),
        ));
    }

    public function deleteAction($args)
    {

        // Get page by id
        $page = $this->getPageById($args['id']);

        // Delete page
        if ($page->delete()) {
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
            $postData = $this->getRequest()->getPostData();

            // Get page by id
            $page = $this->getPageById($postData->get('page_id'));

            // Get navitem of page
            $navitem = $page->navitems()
                ->where('navigation_id', '=', 1)
                ->fetch();

            // Update page
            $page->title = $postData->get('title');
            $page->is_active = $postData->get('is_active');
            $page->visibility = $postData->get('visibility');

            // Update navitem
            $navitem->parent_navitem_id = $postData->parent_navitem_id ? : null;

            // Save page and navitem
            if ($page->validate() && $page->save() && $navitem->save()) {
                $this->setSuccessAlert(translate('{0} successful updated', array('Page')));
            } else {
                $this->setDangerAlert(translate('Update failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('page_settings', array('id' => $page->id()));
    }

    /**
     * Activate page action.

     *

     * @param  array    $args
     * @return Response
     */
    public function activateAction($args)
    {

        // Get page by id
        $page = $this->getPageById($args['id']);

        // Set state
        $page->is_active = !$page->is_active;

        // Save page
        if ($page->validate() && $page->save()) {
            if ($page->is_active) {
                $this->setSuccessAlert(translate('Page successful activated'));
            } else {
                $this->setSuccessAlert(translate('Page successful disabled'));
            }
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

    protected function getPageById($id)
    {

        // Get page by id
        $page = PageModel::findById($id);

        if ($page) {
            return $page;
        }

        throw new \Exception('Page not found');
    }
}
