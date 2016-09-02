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

        // Set title
        $this->view
            ->setSubtitle('Accounts')
            ->setTitle('Roles');
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
            $postData = $this->request()->getPostData();

            // Create role
            $role = RoleModel::create(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'permission_ids' => $postData->get('permission_ids'),
            ));

            // Validate and save role
            if ($role && $role->validate() && $role->save()) {
                $this->setSuccessAlert(translate('Successful created'));
            } else {
                throw new Exception('Create role failed');
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
     * @return Response
     */
    public function editAction($args)
    {

        // Get user or data if validation has failed
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $role = new RoleModel($data);
        } else {
            $role = RoleModel::findById($args['id']);
            if (!$role) {
                throw new Exception('Role not found (ID: ' . $args['id'] . ')');
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
     *
     * @throws Exception
     */
    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Update role
            $role = RoleModel::update(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'permission_ids' => $postData->get('permission_ids'),
                    ), $postData->get('role_id'));

            // Validate and save role
            if ($role && $role->validate() && $role->save()) {
                $this->setSuccessAlert(translate('Successful updated'));
            } else {
                throw new Exception('Update role failed (ID: ' . $postData->get('page_id') . ')');
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
     *
     * @throws Exception
     */
    public function deleteAction($args)
    {
        try {

            // Delete role
            $result = RoleModel::deleteById($args['id']);
            if ($result) {
                $this->setSuccessAlert(translate('Successful deleted'));
            } else {
                throw new Exception('Role not found or delete failed (ID: ' . $args['id'] . ')');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('role_index');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission()
    {
        return has_permission('manage_roles');
    }
}
