<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\RoleModel;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;
use function has_permission;
use function translate;

class UserController extends BackendController
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
            ->setTitle('Users');
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
        return $this->render('backend/user/index', array(
                'roles' => RoleModel::findAll(),
                'users' => UserModel::findAll(),
        ));
    }

    /**
     * Create user action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function createAction($args)
    {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Create user
            $user = UserModel::create(array(
                    'email' => $postData->get('email'),
                    'firstname' => $postData->get('firstname'),
                    'lastname' => $postData->get('lastname'),
                    'role_id' => $postData->get('role_id'),
                    'password' => $postData->get('password'),
                    'password2' => $postData->get('password2'),
            ));

            // Validate and save user
            if ($user->validate() && $user->validatePassword() && $user->save()) {
                $this->setSuccessAlert(translate('Successful created'));
            } else {
                throw new Exception('Create user failed');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Edit user action.
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
            $user = UserModel::update($data, $data['user_id']);
        } else {
            $user = UserModel::findById($args['id']);
            if (!$user) {
                throw new Exception('User found (ID: ' . $args['id'] . ')');
            }
        }

        // Set back url
        $this->view->setBackRoute('user_index');

        return $this->render('backend/user/edit', array(
                'user' => $user,
                'roles' => RoleModel::findAll(),));
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

            // Update user
            $user = UserModel::update(array(
                    'email' => $postData->get('email'),
                    'firstname' => $postData->get('firstname'),
                    'lastname' => $postData->get('lastname'),
                    'role_id' => $postData->get('role_id'),
                    ), $postData->get('user_id'));

            // Validate and save user
            if ($user->validate() && $user->save()) {
                $this->setSuccessAlert(translate('Successful updated'));
            } else {
                throw new Exception('Update user failed (ID: ' . $postData->get('user_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('user_edit', array('id' => $postData->get('user_id')));
    }

    /**
     * Update user password action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function updatePasswordAction($args)
    {
        try {

            // Get post data
            $postData = $this->request()->getPostData();

            // Update user password
            $user = UserModel::updatePassword($postData->get('password'), $postData->get('password2'), $postData->get('user_id'));

            // Validate and save user password
            if ($user->validatePassword() && $user->save()) {
                $this->setSuccessAlert(translate('Password successful updated'));
            } else {
                throw new Exception('Update password of user failed (ID: ' . $postData->get('navitem_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->setDangerAlert($ex->getErrors());
        }

        return $this->redirectToRoute('user_edit', array('id' => $postData->get('user_id')));
    }

    /**
     * Delete user action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function deleteAction($args)
    {
        // Delete user
        $result = UserModel::deleteById($args['id']);
        if ($result) {
            return $this
                    ->setSuccessAlert(translate('{0} successful deleted', array('User')))
                    ->redirectToRoute('user_index');
        }
        throw new Exception('Delete user failed (ID: ' . $args['id'] . ')');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    public function checkPermission()
    {
        return has_permission('manage_users');
    }
}
