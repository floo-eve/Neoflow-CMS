<?php

namespace Lahaina\CMS\Controller\Backend;

use \Lahaina\CMS\Controller\BackendController;
use \Lahaina\CMS\Mapper\LanguageMapper;
use \Lahaina\CMS\Mapper\ModuleMapper;
use \Lahaina\CMS\Mapper\NavitemMapper;
use \Lahaina\CMS\Mapper\PageMapper;
use \Lahaina\CMS\Model\NavitemModel;
use \Lahaina\CMS\Model\PageModel;
use \Lahaina\CMS\Views\Backend\PageView;
use \Lahaina\Framework\Handler\Validation\ValidationException;
use \Lahaina\Framework\HTTP\Responsing\Response;
use \Lahaina\Helper\Alert\DangerAlert;
use \Lahaina\Helper\Alert\SuccessAlert;
use \Lahaina\Helper\Alert\WarningAlert;

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

        return $this->redirectToRoute('page_edit', array('id' => $page->id(), 'language_id' => $page->language_id));
    }

    public function editAction($args)
    {
        // Get page by id
        $page = $this->pageMapper->findById($args['id']);

        if ($page) {

        }
        $this->getSession()
            ->setFlash('alert', new WarningAlert('Page not found'));

        return $this->redirectToRoute('page_index');
    }

    public function deleteAction($args)
    {
        // Get page by id
        $page = $this->pageMapper->findById($args['id']);

        // Delete page
        try {
            if ($page && $page->delete()) {
                $this->getSession()
                    ->setFlash('alert', new SuccessAlert('Page successful deleted'));
            } else {
                $this->getSession()
                    ->setFlash('alert', new WarningAlert('Page not found'));
            }
        } catch (ValidationException $ex) {
            $this->getSession()
                ->setFlash('alert', new WarningAlert($ex->getMessage()));
        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Set view.
     */
    public function setView()
    {
        $this->view = new PageView();
    }
}
