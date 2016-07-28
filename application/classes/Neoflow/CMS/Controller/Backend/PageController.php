<?php

namespace Neoflow\CMS\Controller\Backend;

use \Neoflow\CMS\Controller\BackendController;
use \Neoflow\CMS\Mapper\LanguageMapper;
use \Neoflow\CMS\Mapper\ModuleMapper;
use \Neoflow\CMS\Mapper\NavitemMapper;
use \Neoflow\CMS\Mapper\PageMapper;
use \Neoflow\CMS\Mapper\SectionMapper;
use \Neoflow\CMS\Model\NavitemModel;
use \Neoflow\CMS\Model\PageModel;
use \Neoflow\CMS\Views\Backend\PageView;
use \Neoflow\Framework\Handler\Validation\ValidationException;
use \Neoflow\Framework\Handler\Validation\ValidationHelper;
use \Neoflow\Framework\HTTP\Responsing\Response;
use \Neoflow\Helper\Alert\DangerAlert;
use \Neoflow\Helper\Alert\SuccessAlert;

class PageController extends BackendController
{

    /**
     * @var LanguageMapper
     */
    protected $languageMapper;

    /**
     * @var PageMapper
     */
    protected $pageMapper;

    /**
     * @var NavitemMapper
     */
    protected $navitemMapper;

    /**
     * @var ModuleMapper
     */
    protected $moduleMapper;

    /**
     * @var SectionMapper
     */
    protected $sectionMapper;

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

        // Create mapper
        $this->languageMapper = new LanguageMapper();
        $this->pageMapper = new PageMapper();
        $this->navitemMapper = new NavitemMapper();
        $this->moduleMapper = new ModuleMapper();
        $this->sectionMapper = new SectionMapper();
    }

    public function indexAction($args)
    {
        // Get all languages
        $languages = $this->languageMapper->findAllBy(array(
            array('is_active', '=', true)
        ));

        // Get page language id
        $language_id = $this->getRequest()->getGet('language_id');
        if (!$language_id) {
            if ($this->getSession()->has('page_language_id')) {
                $language_id = $this->getSession()->get('page_language_id');
            } else {
                $language_id = $languages[0]->id();
                $this->getSession()->reflash();
                return $this->redirectToRoute('page_index', array('language_id' => $language_id));
            }
        }
        $this->getSession()->set('page_language_id', $language_id);

        // Get page language
        $pageLanguage = $this->languageMapper->findById($language_id);

        // Get navitems
        $navitems = $this->navitemMapper->getOrm()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $pageLanguage->id())
            ->where('navigation_id', '=', 1)
            ->orderByAsc('position')
            ->fetchAll();

        // Get modules
        $modules = $this->moduleMapper->findAll();

        return $this->render('backend/page/index', array(
                'languages' => $languages,
                'pageLanguage' => $pageLanguage,
                'navitems' => $navitems,
                'modules' => $modules,
        ));
    }

    /**
     * Create action
     *
     * @param array $args
     * @return Response
     */
    public function createAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Create page
            $page = new PageModel();
            $page->title = $postData->get('title');
            $page->language_id = $postData->get('language_id');
            $page->is_active = $postData->get('is_active');

            // Save page
            if ($page->save()) {

                // Create navitem
                $navitem = new NavitemModel();
                $navitem->navigation_id = 1;
                $navitem->page_id = $page->id();
                $navitem->language_id = $page->language_id;
                $navitem->parent_navitem_id = $postData->parent_navitem_id ? : null;

                // Create section
                $section = new \Neoflow\CMS\Model\SectionModel();
                $section->page_id = $page->id();
                $section->module_id = $postData->get('module_id');
                $section->is_active = true;
                $section->block = 1;

                // Save navitem
                if ($navitem->save() && $section->save()) {
                    $this->setFlash('alert', new SuccessAlert('Page successful saved'));
                }
            }
        } catch (ValidationException $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getErrors()));
        }
        return $this->redirectToRoute('page_index', array('language_id' => $page->language_id));
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
        $modules = $this->moduleMapper->findAll();

        // Set back url
        $this->view->setBackRoute('page_index', array('language_id' => $page->language_id));

        return $this->render('backend/page/sections', array(
                'page' => $page,
                'modules' => $modules,
                'sections' => $sections
        ));
    }

    public function settingsAction($args)
    {

        // Create validation helper
        $validationHelper = new ValidationHelper();

        // Get page data if validation has failed)
        if ($validationHelper->hasError()) {
            $pageData = $validationHelper->getData();
            $page = new PageModel($pageData);
        } else {

            // Get page by id
            $page = $this->getPageById($args['id']);
        }

        // Get navitems
        $navitems = $this->navitemMapper->getOrm()
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
                'disabledNavitemIds' => array($navitem->id())
        ));
    }

    public function deleteAction($args)
    {
        // Get page by id
        $page = $this->getPageById($args['id']);

        // Delete page
        if ($page->delete()) {
            $this->setFlash('alert', new SuccessAlert('Page successful deleted'));
        }
        return $this->redirectToRoute('page_index');
    }

    /**
     * Update page action
     *
     * @param array $args
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
            if ($page->save() && $navitem->save()) {
                $this->setFlash('alert', new SuccessAlert('Page successful saved'));
            }
        } catch (ValidationException $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getErrors()));
        }
        return $this->redirectToRoute('page_settings', array('id' => $page->id()));
    }

    /**
     * Activate page action
     *
     * @param array $args
     * @return Response
     */
    public function activateAction($args)
    {
        // Get page by id
        $page = $this->getPageById($args['id']);

        // Set state
        $page->is_active = !$page->is_active;

        // Save page
        if ($page->save()) {
            if ($page->is_active) {
                $this->setFlash('alert', new SuccessAlert('Page successful activated'));
            } else {
                $this->setFlash('alert', new SuccessAlert('Page successful disabled'));
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
        $page = $this->pageMapper->findById($id);

        if ($page) {
            return $page;
        }

        throw new \Exception('Page not found');
    }
}
