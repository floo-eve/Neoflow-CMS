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
use \Neoflow\CMS\Model\SectionModel;
use \Neoflow\CMS\Views\Backend\PageView;
use \Neoflow\Framework\Handler\Validation\ValidationException;
use \Neoflow\Framework\HTTP\Responsing\Response;
use \Neoflow\Helper\Alert\DangerAlert;
use \Neoflow\Helper\Alert\SuccessAlert;
use \Neoflow\Helper\Alert\WarningAlert;

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

        $language_id = $this->getRequest()->getGet('language_id');
        if (!$language_id) {
            if ($this->getSession()->has('language_id')) {
                $language_id = $this->getSession()->get('language_id');
            } else {
                $this->getSession()->reflash();
                $args['language_id'] = $languages[0]->id();
                return $this->redirectToRoute('page_index', $args);
            }
        }
        $this->getSession()->set('language_id', $language_id);
        $activeLanguage = $this->languageMapper->findById($language_id);

        $navitems = $this->navitemMapper->getOrm()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $activeLanguage->id())
            ->where('navigation_id', '=', 1)
            ->orderByAsc('position')
            ->fetchAll();

        return $this->render('backend/page/index', array(
                'languages' => $languages,
                'activeLanguage' => $activeLanguage,
                'navitems' => $navitems
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
        // Get post data
        $postData = $this->getRequest()->getPostData();

        try {

            $page = new PageModel();
            $page->title = $postData->get('title');
            $page->language_id = $postData->get('language_id');

            if ($page->save()) {
                $navitem = new NavitemModel();
                $navitem->navigation_id = 1;
                $navitem->page_id = $page->id();
                $navitem->language_id = $page->language_id;
                $navitem->parent_navitem_id = $postData->parent_navitem_id ? : null;
                $navitem->save();
            }
        } catch (ValidationException $ex) {

            // Fallback if validation fails
            $this->getSession()
                ->setFlash('alert', new DangerAlert($ex->getErrors()));

            return $this->redirectToRoute('page_index');
        }

        $this->getSession()
            ->setFlash('alert', new SuccessAlert('Page successful created'));

        return $this->redirectToRoute('page_sections', array('id' => $page->id(), 'language_id' => $page->language_id));
    }

    public function sectionsAction($args)
    {
        // Get page by id
        $page = $this->getPageById($args['id']);

        $sections = $page->sections()
            ->orderByAsc('position')
            ->fetchAll();

        $modules = $this->moduleMapper->findAll();

        return $this->render('backend/page/sections', array(
                'page' => $page,
                'modules' => $modules,
                'sections' => $sections
        ));
    }

    public function editAction($args)
    {
        // Get page by id
        $page = $this->getPageById($args['id']);

        $navitems = $this->navitemMapper->getOrm()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $page->language_id)
            ->where('navigation_id', '=', 1)
            ->orderByAsc('position')
            ->fetchAll();

        $navitem = $page->navitems()
            ->where('navigation_id', '=', 1)
            ->fetch();

        $parentNavitemId = false;
        $parentNavitem = $navitem->parentNavitem()->fetch();
        if ($parentNavitem) {
            $parentNavitemId = $parentNavitem->id();
        }

        return $this->render('backend/page/settings', array(
                'page' => $page,
                'navitems' => $navitems,
                'parentNavitemId' => $parentNavitemId
        ));
    }

    public function deleteAction($args)
    {
        // Get page by id
        $page = $this->getPageById($args['id']);

        // Delete page
        try {
            if ($page->delete()) {
                $this->getSession()
                    ->setFlash('alert', new SuccessAlert('Page successful deleted'));
            } else {
                $this->getSession()
                    ->setFlash('alert', new WarningAlert('Delete page failed'));
            }
        } catch (ValidationException $ex) {
            $this->getSession()
                ->setFlash('alert', new WarningAlert($ex->getMessage()));
        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Update settings action
     *
     * @param array $args
     * @return Response
     */
    public function updateSettingsAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Get page by id
            $page = $this->getPageById($postData->get('page_id'));

            $navitem = $page->navitems()
                ->where('navigation_id', '=', 1)
                ->fetch();

            $page->title = $postData->get('title');
            $page->is_active = $postData->get('is_active');
            $page->visibility = $postData->get('visibility');

            $navitem->parent_navitem_id = $postData->parent_navitem_id ? : null;

            if ($page->save() && $navitem->save()) {
                $this->getSession()
                    ->setFlash('alert', new SuccessAlert('Page successful updated'));
            } else {
                $this->getSession()
                    ->setFlash('alert', new DangerAlert('Update page failed'));
            }
        } catch (ValidationException $ex) {

            // Fallback if validation fails
            $this->getSession()
                ->setFlash('alert', new DangerAlert($ex->getErrors()));

            return $this->redirectToRoute('page_index');
        }
        return $this->redirectToRoute('page_edit', array('id' => $page->id()));
    }

    /**
     * Set view.
     */
    public function setView()
    {
        $this->view = new PageView();
    }

    public function getPageById($id)
    {
        // Get page by id
        $page = $this->pageMapper->findById($id);

        if ($page) {
            return $page;
        }

        throw new \Exception('Page not found');
    }
}
