<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Views\Backend\NavigationView;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;
use function translate;

class NavigationController extends BackendController
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
            ->setTitle('Navigations');
    }

    /**
     * Index action
     *
     * @param array $args
     * @return Response
     */
    public function indexAction($args)
    {
        $navigations = NavigationModel::repo()
            ->where('navigation_id', '!=', 1)
            ->fetchAll();

        return $this->render('backend/navigation/index', array(
                'navigations' => $navigations,
        ));
    }

    public function navitemsAction($args)
    {
        // Get navigation by id
        $navigation = NavigationModel::findById($args['id']);
        if (!$navigation || $navigation->id() == 1) {
            $this->setDangerAlert(translate('{0} not found', array('Navigation')));

            return $this->redirectToRoute('navigation_index');
        }

        // Get all languages
        $languages = LanguageModel::findAllByColumn('is_active', true);

        // Get navigation language id
        $language_id = $this->getRequest()->getGet('language_id');

        if (!$language_id) {
            if ($this->session()->has('navigation_language_id')) {
                $language_id = $this->session()->get('navigation_language_id');
            } else {
                $language_id = $languages[0]->id();
                $this->session()->reflash();
                return $this->redirectToRoute('navigation_navitems', array('id' => $navigation->id(), 'language_id' => $language_id));
            }
        }
        $this->session()->set('navigation_language_id', $language_id);

        // Get navigation language
        $navigationLanguage = LanguageModel::findById($language_id);

        $navitems = $navigation->navitems()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $language_id)
            ->where('parent_navitem_id', 'IS', null)
            ->orderByAsc('position')
            ->fetchAll();

        $pageNavitems = NavitemModel::repo()
            ->where('navigation_id', '=', 1)
            ->where('language_id', '=', $language_id)
            ->where('parent_navitem_id', 'IS', null)
            ->orderByAsc('position')
            ->fetchAll();

        // Set back url
        $this->view->setBackRoute('navigation_index', array('language_id' => $navigation->language_id));

        return $this->render('backend/navigation/navitems', array(
                'navigation' => $navigation,
                'navitems' => $navitems,
                'pageNavitems' => $pageNavitems,
                'languages' => $languages,
                'navigationLanguage' => $navigationLanguage,
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
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Create navigation
            $navigation = NavigationModel::create(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description')
            ));

            if ($navigation->validate() && $navigation->save()) {
                $this->setSuccessAlert(translate('{0} successful created', array('Navigation')));
            } else {
                $this->setDangerAlert(translate('Create failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('navigation_index');
    }

    public function editAction($args)
    {

        // Get navigation data if validation has failed)
        if ($this->service('validation')->hasError()) {
            $navigationData = $this->service('validation')->getData();

            $navigation = new PageModel($navigationData);
        } else {

            // Get navigation by id
            $navigation = NavigationModel::findById($args['id']);
            if (!$navigation || $navigation->id() == 1) {
                $this->setDangerAlert(translate('{0} not found', array('Navigation')));

                return $this->redirectToRoute('navigation_index');
            }
        }


        // Set back url
        $this->view->setBackRoute('navigation_index', array('language_id' => $navigation->language_id));

        return $this->render('backend/navigation/edit', array(
                'navigation' => $navigation
        ));
    }

    /**
     * Update action.

     *

     * @param array $args

     * @return Response
     */
    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Get navigation by id
            $navigation = NavigationModel::update(array(
                    'title' => $postData->get('title'),
                    'is_active' => $postData->get('is_active'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id'),
                    'visibility' => $postData->get('visibility'),
                    'module_id' => $postData->get('module_id'),
                    ), $postData->get('navigation_id'));

            // Save navigation and navitem
            if ($navigation->validate() && $navigation->save()) {
                $this->setSuccessAlert(translate('{0} successful updated', array('Navigation')));
            } else {
                $this->setDangerAlert(translate('Update failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('navigation_edit', array('id' => $navigation->id()));
    }

    /**
     * Delete navigation action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function deleteAction($args)
    {
        // Delete navigation
        $result = NavigationModel::deleteById($args['id']);

        if ($result) {
            $this->setSuccessAlert(translate('{0} successful deleted', array('Navigation')));
        } else {
            $this->setDangerAlert(translate('Delete failed'));
        }

        return $this->redirectToRoute('navigation_index');
    }

    /**
     * Set view.
     */
    protected function setView()
    {
        $this->view = new NavigationView();
    }
}
