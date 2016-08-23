<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\PermissionModel;
use Neoflow\CMS\Model\RoleModel;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;

class RoleController extends BackendController
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->view
            ->setTitle('Accounts')
            ->setSubtitle('Roles');
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
        // Get roles
        $roles = RoleModel::repo()
            ->where('role_id', '!=', 1)
            ->fetchAll();

        return $this->render('backend/role/index', array(
                'permissions' => PermissionModel::findAll(),
                'roles' => $roles,
        ));
    }

    /**
     * Create role action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function createAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Create role
            $role = RoleModel::create(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'permission_ids' => $postData->get('permission_ids'),
            ));

            if ($role->validate() && $role->save()) {
                $this->setSuccessAlert(translate('{0} successful created', array('Role')));
            } else {
                $this->setDangerAlert(translate('Create failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('role_index');
    }

    /**
     * Edit role action.
     *
     * @param array $args
     *
     * @return Response|RedirectResponse
     */
    public function editAction($args)
    {

        // Get role if validation has failed
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $role = new RoleModel($data);
        } else {

            // Get role by id
            $role = RoleModel::findById($args['id']);
            if (!$role) {
                $this->setDangerAlert(translate('{0} not found', array('Role')));

                return $this->redirectToRoute('role_index');
            }
        }

        // Set back url
        $this->view->setBackRoute('role_index');

        return $this->render('backend/role/edit', array(
                'role' => $role,
                'permissions' => PermissionModel::findAll(),
        ));
    }

    /**
     * Update role action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Get and update role
            $role = RoleModel::update(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'permission_ids' => $postData->get('permission_ids'),
                    ), $postData->get('role_id'));

            if ($role->validate() && $role->save()) {
                $this->setSuccessAlert(translate('{0} successful updated', array('Role')));
            } else {
                $this->setDangerAlert(translate('Update failed'));

                return $this->redirectToRoute('role_index');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('role_edit', array('id' => $postData->get('role_id')));
    }

    /**
     * Delete role action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function deleteAction($args)
    {
        try {
            // Delete role
            $result = RoleModel::deleteById($args['id']);

            if ($result) {
                $this->setSuccessAlert(translate('{0} successful deleted', array('Role')));
            } else {
                $this->setDangerAlert(translate('Delete failed'));
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('role_index');
    }
}
