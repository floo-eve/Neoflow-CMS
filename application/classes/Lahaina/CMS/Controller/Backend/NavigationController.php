<?php

namespace Lahaina\CMS\Controller\Backend;

use \Lahaina\CMS\Controller\BackendController;
use \Lahaina\CMS\Mapper\LanguageMapper;
use \Lahaina\CMS\Mapper\NavigationMapper;
use \Lahaina\CMS\Mapper\NavitemMapper;
use \Lahaina\CMS\Mapper\PageMapper;
use \Lahaina\CMS\Model\NavigationModel;
use \Lahaina\CMS\Model\NavitemModel;
use \Lahaina\CMS\Views\Backend\NavigationView;
use \Lahaina\Framework\Handler\Validation\ValidationException;
use \Lahaina\Framework\HTTP\Responsing\JsonResponse;
use \Lahaina\Framework\HTTP\Responsing\Response;
use \Lahaina\Helper\Alert\DangerAlert;
use \Lahaina\Helper\Alert\SuccessAlert;
use function \is_json;

class NavigationController extends BackendController
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
     * @var NavigationMapper
     */
    protected $navigationMapper;

    /**
     * @var NavitemMapper
     */
    protected $navitemMapper;

    /**
     * @var array
     */
    protected $languages = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->view
            ->setSubtitle('Backend / Content')
            ->setTitle('Navigations');

        // Create mapper
        $this->languageMapper = new LanguageMapper();
        $this->pageMapper = new PageMapper();
        $this->navigationMapper = new NavigationMapper();
        $this->navitemMapper = new NavitemMapper();
    }

    public function indexAction($args)
    {
        $navigations = $this->navigationMapper->findAll();

        return $this->render('backend/navigation/index', array(
                'navigations' => $navigations,
        ));
    }

    public function editAction($args)
    {
        $navigation = $this->navigationMapper->findById($args['id']);
        $languages = $this->languageMapper->getOrm()
            ->where('is_active', '=', true)
            ->fetchAll();

        $language_id = $this->getRequest()->getGet('language_id');
        if (!$language_id) {
            $args['language_id'] = $languages[0]->id();
            return $this->redirectToRoute('navigation_edit', $args);
        }
        $activeLanguage = $this->languageMapper->findById($language_id);

        $navitems = $navigation->navitems()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $language_id)
            ->fetchAll();

        return $this->render('backend/navigation/edit', array(
                'languages' => $languages,
                'navigation' => $navigation,
                'navitems' => $navitems,
                'activeLanguage' => $activeLanguage,
        ));
    }

    public function updateNavitemOrderAction($args)
    {
        $json = file_get_contents('php://input');
        $result = false;
        if (is_json($json)) {
            $result = $this->navitemMapper->updateOrder(json_decode($json, true));
        }
        return new JsonResponse(array('success' => (bool) $result));
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
        // Get post data
        $postData = $this->getRequest()->getPostData();
        $navigation_id = $postData->get('navigation_id');

        try {

            // Create model entity
            $navigation = new NavigationModel();
            $navigation->title = $postData->get('title');
            $navigation->description = $postData->get('description');
            $navigation->save();
        } catch (ValidationException $ex) {

            // Fallback if validation fails
            $this->getSession()
                ->setFlash('alert', new DangerAlert($ex->getErrors()));

            return $this->redirectToRoute('navigation_index');
        } catch (Exception $ex) {

            // Fallback if something got wrong
            $this->getSession()
                ->setFlash('alert', new DangerAlert('Something got wrong'));

            return $this->redirectToRoute('navigation_index');
        }

        $this->getSession()
            ->setFlash('alert', new SuccessAlert('Successful saved'));

        return $this->redirectToRoute('navigation_index');
    }

    /**
     * Update action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function updateAction($args)
    {
        // Get post data
        $postData = $this->getRequest()->getPostData();
        $navigation_id = $postData->get('navigation_id');

        try {

            // Update model entity
            $navigation = $this->navigationMapper->findById($navigation_id);
            $navigation->title = $postData->get('title');
            $navigation->description = $postData->get('description');
            $navigation->save();
        } catch (ValidationException $ex) {

            // Fallback if validation fails
            $this->getSession()
                ->setFlash('alert', new DangerAlert($ex->getErrors()));

            return $this->redirectToRoute('navigation_edit', array('id' => $navigation_id));
        } catch (Exception $ex) {

            // Fallback if something get wrong
            $this->getSession()
                ->setFlash('alert', new DangerAlert('Something get wrong'));

            return $this->redirectToRoute('navigation_index');
        }

        $this->getSession()
            ->setFlash('alert', new SuccessAlert('Successful saved'));

        return $this->redirectToRoute('navigation_edit', array('id' => $navigation->id()));
    }

    /**
     * Add item action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function addItemAction($args)
    {
        // Get post data
        $postData = $this->getRequest()->getPostData();

        // Get model entities
        $navitem = new NavitemModel();

        try {

            // Save setting
            $navitem->title = $postData->get('title');
            $navitem->page_id = $postData->get('page_id');
            $navitem->language_id = $postData->get('language_id');
            $navitem->navigation_id = $postData->get('navigation_id');
            $navitem->save();
        } catch (ValidationException $ex) {

            // Fallback if validation fails
            $this->getSession()
                ->setFlash('alert', new DangerAlert($ex->getErrors()));

            return $this->redirectToRoute('navigation_index');
        }

        $this->getSession()
            ->setFlash('alert', new SuccessAlert('Successful added'));

        return $this->redirectToRoute('navigation_edit', array('id' => $navitem->navigation_id, 'language_id' => $navitem->language_id));
    }

    /**
     * Set view.
     */
    public function setView()
    {
        $this->view = new NavigationView();
    }
}
