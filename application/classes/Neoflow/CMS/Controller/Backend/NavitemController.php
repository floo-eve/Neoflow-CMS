<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Views\Backend\NavitemView;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;
use function has_permission;
use function is_json;
use function translate;

class NavitemController extends BackendController
{

    public function __construct()
    {
        parent::__construct();

        // Set title
        $this->view
            ->setSubtitle('Content')
            ->setTitle('Navigations');
    }

    /**
     * Index action.
     *
     * @param array $args
     *
     * @return type
     *
     * @throws Response|RedirectResponse
     */
    public function indexAction($args)
    {
        // Get navigation by id
        $navigation = NavigationModel::findById($args['id']);
        if (!$navigation || $navigation->id() == 1) {
            throw new Exception('Navigation not found or inaccessible (ID: ' . $args['id'] . ')');
        }

        // Get languages
        $languages = LanguageModel::findAllByColumn('is_active', true);

        // Get id of current language
        $language_id = $this->request()->getGet('language_id');
        if (!$language_id) {
            if ($this->session()->has('navigation_language_id')) {
                $language_id = $this->session()->get('navigation_language_id');
            } else {
                $language = LanguageModel::findAllByColumn('is_active', true)->first();
                $this->session()->reflash();

                return $this->redirectToRoute('navitem_index', array('id' => $navigation->id(), 'language_id' => $language->id()));
            }
        }
        $this->session()->set('navigation_language_id', $language_id);

        // Get navigation items for selectable pages
        $pageNavitems = NavitemModel::repo()
            ->where('navigation_id', '=', 1)
            ->where('language_id', '=', $language_id)
            ->where('parent_navitem_id', 'IS', null)
            ->orderByAsc('position')
            ->fetchAll();

        // Get language of navigation
        $navigationLanguage = LanguageModel::findById($language_id);

        // Get navigation items of navigation and current language
        $navitems = $navigation->navitems()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $language_id)
            ->where('parent_navitem_id', 'IS', null)
            ->orderByAsc('position')
            ->fetchAll();

        // Set back url
        $this->view->setBackRoute('navigation_index', array('language_id' => $navigation->language_id));

        return $this->render('backend/navitem/index', array(
                'navigation' => $navigation,
                'navitems' => $navitems,
                'pageNavitems' => $pageNavitems,
                'languages' => $languages,
                'navigationLanguage' => $navigationLanguage,
        ));
    }

    /**
     * Create navigation item action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function createAction($args)
    {
        // Prevent access for page permissions
        if (has_permission('manage_pages') && !has_permission('manage_navigations')) {
            return $this->unauthorizedAction();
        }

        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Create navigation item
            $navitem = NavitemModel::create(array(
                    'title' => $postData->get('title'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id') ? : null,
                    'navigation_id' => $postData->get('navigation_id'),
                    'language_id' => $postData->get('language_id'),
                    'page_id' => $postData->get('page_id'),
                    'is_visible' => $postData->get('is_visible'),
            ));

            // Validate and save navigation item
            if ($navitem && $navitem->validate() && $navitem->save()) {
                $this->setSuccessAlert(translate('Successful created'));
            } else {
                throw new Exception('Create navigation item failed');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('navitem_index', array('id' => $navitem->navigation_id));
    }

    /**
     * Delete navigation item action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function deleteAction($args)
    {
        // Prevent access for page permissions
        if (has_permission('manage_pages') && !has_permission('manage_navigations')) {
            return $this->unauthorizedAction();
        }

        // Get and delete navigation item
        $navitem = NavitemModel::findById($args['id']);
        if ($navitem && $navitem->delete()) {
            return $this
                    ->setSuccessAlert(translate('Successful deleted'))
                    ->redirectToRoute('navitem_index', array('id' => $navitem->navigation_id));
        }
        throw new Exception('Delete navigation item failed (ID: ' . $args['id'] . ')');
    }

    /**
     * Edit navigation item action.
     *
     * @param array $args
     *
     * @return Response|RedirectResponse
     *
     * @throws Exception
     */
    public function editAction($args)
    {
        // Get navigation item or data if validation has failed
        if ($this->service('validation')->hasError()) {
            $navitemData = $this->service('validation')->getData();
            $navitem = new PageModel($navitemData);
        } else {
            $navitem = NavitemModel::findById($args['id']);
            if (!$navitem) {
                throw new Exception('Navigation item not found (ID: ' . $args['id'] . ')');
            }
        }

        // Set back url
        $this->view->setBackRoute('navitem_index', array('id' => $navitem->navigation_id));

        // Get navigation
        $navigation = $navitem->navigation()->fetch();

        // Get navigation items for selectable pages
        $pageNavitems = NavitemModel::repo()
            ->where('navigation_id', '=', 1)
            ->where('language_id', '=', $navitem->language_id)
            ->where('parent_navitem_id', 'IS', null)
            ->orderByAsc('position')
            ->fetchAll();

        // Get navigation items of navigation and current language
        $navitems = $navigation->navitems()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $navitem->language_id)
            ->where('parent_navitem_id', 'IS', null)
            ->orderByAsc('position')
            ->fetchAll();

        return $this->render('backend/navitem/edit', array(
                'navitem' => $navitem,
                'navigation' => $navigation,
                'pageNavitems' => $pageNavitems,
                'navitems' => $navitems,
        ));
    }

    /**
     * Update navigation item action.
     *
     * @param array $args
     *
     * @return Response
     *
     * @throws Exception
     */
    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Update navitem
            $navitem = NavitemModel::update(array(
                    'title' => $postData->get('title'),
                    'is_visible' => $postData->get('is_visible'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id') ? : null,
                    'page_id' => $postData->get('page_id'),
                    ), $postData->get('navitem_id'));

            // Validate and save navigation item
            if ($navitem && $navitem->validate() && $navitem->save()) {
                $this->setSuccessAlert(translate('Successful updated'));
            } else {
                throw new Exception('Update navigation item failed (ID: ' . $postData->get('navitem_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('navitem_edit', array('id' => $navitem->id()));
    }

    /**
     * Toggle visibility of navigation item action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function toggleVisiblityAction($args)
    {
        // Prevent access for page permissions
        if (has_permission('manage_pages') && !has_permission('manage_navigations')) {
            return $this->unauthorizedAction();
        }

        // Get navigation item and toggle visiblity
        $navitem = NavitemModel::findById($args['id']);
        if ($navitem && $navitem->toggleVisibility() && $navitem->save()) {
            if ($navitem->is_visible) {
                $this->setSuccessAlert(translate('Successful made visible'));
            } else {
                $this->setSuccessAlert(translate('Successful hidden'));
            }
            return $this->redirectToRoute('navitem_index', array('id' => $navitem->navigation_id));
        }
        throw new Exception('Navigation item not found or toggle visibility failed (ID: ' . $args['id'] . ')');
    }

    /**
     * Reorder navigation items action.
     *
     * @param array $args
     *
     * @return JsonResponse
     */
    public function reorderAction($args)
    {
        // Get json request
        $json = file_get_contents('php://input');

        // Reorder and update navigation item
        $result = false;
        if (is_json($json)) {
            $result = $this
                ->service('navitem')
                ->updateOrder(json_decode($json, true));
        }
        return new JsonResponse(array('success' => $result));
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission()
    {
        return has_permission('manage_navigations') || has_permission('manage_pages');
    }

    /**
     * Set view.
     */
    protected function setView()
    {
        $this->view = new NavitemView();
    }
}
