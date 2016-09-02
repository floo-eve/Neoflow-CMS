<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;
use Exception;

class NavigationController extends BackendController
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
            ->setTitle('Navigations');
    }

    /**
     * Index action.
     *
     * @param array $args
     *
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

    /**
     * Create navigation action.
     *
     * @param array $args
     *
     * @return Response
     *
     * @throws Exception
     */
    public function createAction($args)
    {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Create navigation
            $navigation = NavigationModel::create(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
            ));

            // Validate and save navigation
            if ($navigation && $navigation->validate() && $navigation->save()) {
                $this->setSuccessAlert(translate('Successful created'));
            } else {
                throw new Exception('Create navigation failed');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('navigation_index');
    }

    /**
     * Edit navigation action.
     *
     * @param array $args
     *
     * @return Response
     *
     * @throws Exception
     */
    public function editAction($args)
    {

        // Get user or data if validation has failed
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $navigation = NavigationModel::update($data, $data['user_id']);
        } else {
            $navigation = NavigationModel::findById($args['id']);
            if (!$navigation || $navigation->id() == 1) {
                throw new Exception('Navigation not found or inaccessible (ID: ' . $args['id'] . ')');
            }
        }

        // Set back url
        $this->view->setBackRoute('navigation_index');

        return $this->render('backend/navigation/edit', array(
                'navigation' => $navigation,
        ));
    }

    /**
     * Update user action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Update navigation
            $navigation = NavigationModel::update(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    ), $postData->get('navigation_id'));

            // Validate and save user
            if ($navigation && $navigation->validate() && $navigation->save()) {
                $this->setSuccessAlert(translate('Successful updated'));
            } else {
                throw new Exception('Navigation not found or delete failed (ID: ' . $postData->get('navigation_id') . ')');
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
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function deleteAction($args)
    {
        // Get and delete navigation
        $navigation = NavigationModel::findById($args['id']);
        if ($navigation && $navigation->id() != 1 && $navigation->delete()) {
            return $this
                    ->setSuccessAlert(translate('Successful deleted'))
                    ->redirectToRoute('navigation_index');
        }
        throw new Exception('Navigation not found or inaccessible (ID: ' . $args['id'] . ')');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission()
    {
        return has_permission('manage_navigations');
    }
}
