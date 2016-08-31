<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Views\Backend\NavitemView;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
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

    public function indexAction($args)
    {
        // Get navigation by id
        $navigation = NavigationModel::findById($args['id']);
        if (!$navigation || $navigation->id() == 1) {
            $this->setDangerAlert(translate('{0} not found', array('Navigation')));

            return $this->redirectToRoute('navigation_index');
        }

        // Get languages
        $languages = LanguageModel::findAllByColumn('is_active', true);

        // Get navigation language id
        $language_id = $this->getRequest()->getGet('language_id');

        if (!$language_id) {
            if ($this->session()->has('navigation_language_id')) {
                $language_id = $this->session()->get('navigation_language_id');
            } else {
                $languages = LanguageModel::findAllByColumn('is_active', true);
                $language_id = $languages[0]->id();
                $this->session()->reflash();

                return $this->redirectToRoute('navitem_index', array('id' => $navigation->id(), 'language_id' => $language_id));
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

        return $this->render('backend/navitem/index', array(
                'navigation' => $navigation,
                'navitems' => $navitems,
                'pageNavitems' => $pageNavitems,
                'languages' => $languages,
                'navigationLanguage' => $navigationLanguage,
        ));
    }

    /**
     * Reorder navitems action.
     *
     * @param array $args
     *
     * @return JsonResponse
     */
    public function reorderAction($args)
    {
        $json = file_get_contents('php://input');

        $result = false;

        if (is_json($json)) {
            $result = $this
                ->service('navitem')
                ->updateOrder(json_decode($json, true));
        }

        return new JsonResponse(array('success' => $result));
    }

    /**
     * Create navitem action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function createAction($args)
    {
        // Prevent access for page permissions
        if (has_permission('manage_pages') && !has_permission('manage_navigations')) {
            return $this->unauthorizedAction();
        }

        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Create navigation
            $navitem = NavitemModel::create(array(
                    'title' => $postData->get('title'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id') ? : null,
                    'navigation_id' => $postData->get('navigation_id'),
                    'language_id' => $postData->get('language_id'),
                    'page_id' => $postData->get('page_id'),
                    'is_visible' => $postData->get('is_visible'),
            ));

            if ($navitem->navigation_id !== 1 && $navitem->validate() && $navitem->save()) {
                $this->setSuccessAlert(translate('{0} successful created', array('Navigation item')));
            } else {
                $this->setDangerAlert(translate('Create failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('navitem_index', array('id' => $navitem->navigation_id));
    }

    /**
     * Toggle visibility action.
     *
     * @param array $args
     *
     * @return RedirectResponse|Response
     */
    public function toggleVisiblityAction($args)
    {
        // Prevent access for page permissions
        if (has_permission('manage_pages') && !has_permission('manage_navigations')) {
            return $this->unauthorizedAction();
        }

        // Get section
        $navitem = NavitemModel::findById($args['id']);

        if ($navitem) {
            $navitem
                ->toggleVisibility()
                ->save();

            if ($navitem->is_visible) {
                $this->setSuccessAlert(translate('Navigation item successful made visible'));
            } else {
                $this->setSuccessAlert(translate('Navigation item successful hidden'));
            }

            return $this->redirectToRoute('navitem_index', array('id' => $navitem->navigation_id));
        }

        $this->setDangerAlert(translate('{0} not found', array('Navigation item')));

        return $this->redirectToRoute('navigation_index');
    }

    /**
     * Delete navitem action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function deleteAction($args)
    {
        // Prevent access for page permissions
        if (has_permission('manage_pages') && !has_permission('manage_navigations')) {
            return $this->unauthorizedAction();
        }

        // Get navitem
        $navitem = NavitemModel::findById($args['id']);

        if ($navitem) {
            if ($navitem->delete()) {
                $this->setSuccessAlert(translate('{0} successful deleted', array('Navigation item')));
            } else {
                $this->setDangerAlert(translate('Delete failed'));
            }

            return $this->redirectToRoute('navitem_index', array('id' => $navitem->navigation_id));
        }

        $this->setDangerAlert(translate('{0} not found', array('Navigation item')));

        return $this->redirectToRoute('navigation_index');
    }

    public function editAction($args)
    {

        // Get navigation data if validation has failed)
        if ($this->service('validation')->hasError()) {
            $navitemData = $this->service('validation')->getData();

            $navitem = new PageModel($navitemData);
        } else {

            // Get navigation by id
            $navitem = NavitemModel::findById($args['id']);
            if (!$navitem || $navitem->id() == 1) {
                $this->setDangerAlert(translate('{0} not found', array('Navigation')));

                return $this->redirectToRoute('navigation_index');
            }
        }

        // Set back url
        $this->view->setBackRoute('navitem_index', array('id' => $navitem->navigation_id));

        $navigation = $navitem->navigation()->fetch();

        // Get navigation language id
        $language_id = $this->getRequest()->getGet('language_id');

        if ($this->session()->has('navigation_language_id')) {
            $language_id = $this->session()->get('navigation_language_id');
        } else {
            return $this->redirectToRoute('navitem_index', array('id' => $navigation->id()));
        }

        $pageNavitems = NavitemModel::repo()
            ->where('navigation_id', '=', 1)
            ->where('language_id', '=', $language_id)
            ->where('parent_navitem_id', 'IS', null)
            ->orderByAsc('position')
            ->fetchAll();

        $navitems = $navigation->navitems()
            ->where('parent_navitem_id', 'IS', null)
            ->where('language_id', '=', $language_id)
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
     * Update action.
     *
     * @param  array    $args
     * @return Response
     */
    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Update navitem
            $navitem = NavitemModel::update(array(
                    'title' => $postData->get('title'),
                    'is_visible' => $postData->get('is_visible'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id') ? : null,
                    'page_id' => $postData->get('page_id'),
                    ), $postData->get('navitem_id'));

            // Save navitem
            if ($navitem->validate() && $navitem->save()) {
                $this->setSuccessAlert(translate('{0} successful updated', array('Navigation item')));
            } else {
                $this->setDangerAlert(translate('Update failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('navitem_edit', array('id' => $navitem->id()));
    }
}
