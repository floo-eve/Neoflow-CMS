<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\PermissionModel;
use Neoflow\CMS\Model\RoleModel;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Support\Alert\DangerAlert;
use Neoflow\Support\Alert\SuccessAlert;
use Neoflow\Support\Validation\ValidationException;

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
     * @return Response
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
                $this->setFlash('alert', new SuccessAlert('{0} successful created', array('Role')));
            } else {
                $this->setFlash('alert', new DangerAlert('Create failed'));
            }
        } catch (ValidationException $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getErrors()));
        }

        return $this->redirectToRoute('role_index');
    }

    public function editAction($args)
    {

        // Get role if validation has failed
        if ($this->validationService->hasError()) {
            $data = $this->validationService->getData();
            $role = new RoleModel($data);
        } else {

            // Get role by id
            $role = RoleModel::findById($args['id']);
            if (!$role) {
                $this->setFlash('alert', new DangerAlert('{0} not found', array('Role')));

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

    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Update role
            $role = RoleModel::update(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'permission_ids' => $postData->get('permission_ids'),
                    ), $postData->get('role_id'));

            if ($role->validate() && $role->save()) {
                $this->setFlash('alert', new SuccessAlert('{0} successful updated', array('Role')));
            } else {
                $this->setFlash('alert', new DangerAlert('Update failed'));

                return $this->redirectToRoute('role_index');
            }
        } catch (ValidationException $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getErrors()));
        }

        return $this->redirectToRoute('role_edit', array('id' => $postData->get('role_id')));
    }

    public function deleteAction($args)
    {
        try {
            // Delete role
            $result = RoleModel::deleteById($args['id']);

            if ($result) {
                $this->setFlash('alert', new SuccessAlert('{0} successful deleted', array('Role')));
            } else {
                $this->setFlash('alert', new DangerAlert('Delete failed'));
            }
        } catch (ValidationException $ex) {
            $this->setFlash('alert', new DangerAlert($ex->getErrors()));
        }

        return $this->redirectToRoute('role_index');
    }
}
