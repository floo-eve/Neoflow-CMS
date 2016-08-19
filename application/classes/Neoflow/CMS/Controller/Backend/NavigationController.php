<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Mapper\LanguageMapper;
use Neoflow\CMS\Mapper\NavigationMapper;
use Neoflow\CMS\Mapper\NavitemMapper;
use Neoflow\CMS\Mapper\PageMapper;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Views\Backend\NavigationView;
use Neoflow\Framework\Support\Validation\ValidationException;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\CMS\Support\Alert\DangerAlert;
use Neoflow\CMS\Support\Alert\SuccessAlert;

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

        // Set titles

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
        $navigations = NavigationModel::findAll();

        return $this->render('backend/navigation/index', array(
                'navigations' => $navigations,
        ));
    }

    public function editAction($args)
    {
        $navigation = NavigationModel::findById($args['id']);

        $languages = $this->languageMapper->getOrm()
            ->where('is_active', '=', true)
            ->fetchAll();

        $language_id = $this->getRequest()->getGet('language_id');

        if (!$language_id) {
            $args['language_id'] = $languages[0]->id();

            return $this->redirectToRoute('navigation_edit', $args);
        }

        $activeLanguage = \Neoflow\CMS\Model\LanguageModel::findById($language_id);

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

    /**
     * Create action.

     *

     * @param array $args

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

            $this->setDangerAlert($ex->getErrors());

            return $this->redirectToRoute('navigation_index');
        } catch (Exception $ex) {

            // Fallback if something got wrong

            $this->setDangerAlert(translate('Something got wrong'));

            return $this->redirectToRoute('navigation_index');
        }

        $this->session()
            ->setSuccessAlert(translate('Successful saved'));

        return $this->redirectToRoute('navigation_index');
    }

    /**
     * Update action.

     *

     * @param array $args

     * @return Response
     */
    public function updateAction($args)
    {

        // Get post data

        $postData = $this->getRequest()->getPostData();

        $navigation_id = $postData->get('navigation_id');

        try {

            // Update model entity

            $navigation = NavigationModel::findById($navigation_id);

            $navigation->title = $postData->get('title');

            $navigation->description = $postData->get('description');

            $navigation->save();
        } catch (ValidationException $ex) {

            // Fallback if validation fails

            $this->session()
                ->setDangerAlert($ex->getErrors());

            return $this->redirectToRoute('navigation_edit', array('id' => $navigation_id));
        } catch (Exception $ex) {

            // Fallback if something get wrong

            $this->session()
                ->setDangerAlert(translate('Transaction failed'));

            return $this->redirectToRoute('navigation_index');
        }

        $this->session()
            ->setSuccessAlert(translate('Successful saved'));

        return $this->redirectToRoute('navigation_edit', array('id' => $navigation->id()));
    }

    /**
     * Add item action.

     *

     * @param array $args

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

            $this->session()
                ->setDangerAlert($ex->getErrors());

            return $this->redirectToRoute('navigation_index');
        }

        $this->session()
            ->setSuccessAlert(translate('Successful added'));

        return $this->redirectToRoute('navigation_edit', array('id' => $navitem->navigation_id, 'language_id' => $navitem->language_id));
    }

    /**
     * Set view.
     */
    protected function setView()
    {
        $this->view = new NavigationView();
    }
}
