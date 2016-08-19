<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\RoleModel;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\CMS\Support\Alert\DangerAlert;
use Neoflow\CMS\Support\Alert\SuccessAlert;
use Neoflow\Framework\Support\Validation\ValidationException;

class UserController extends BackendController
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->view
            ->setTitle('Accounts')
            ->setSubtitle('Users');
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
     * @return Response
     */
    public function createAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Create user
            $user = UserModel::create(array(
                    'email' => $postData->get('email'),
                    'firstname' => $postData->get('firstname'),
                    'lastname' => $postData->get('lastname'),
                    'role_id' => $postData->get('role_id'),
                    'password' => $postData->get('password'),
                    'password2' => $postData->get('password2'),
            ));

            if ($user->validate() && $user->validatePassword() && $user->save()) {
                $this->setSuccessAlert(translate('{0} successful created', array('User')));
            } else {
                $this->setDangerAlert(translate('Create failed'));
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
     */
    public function editAction($args)
    {
        // Get user or create user with inva
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $user = UserModel::update($data, $data['user_id']);
        } else {

            // Get user by id
            $user = UserModel::findById($args['id']);
            if (!$user) {
                $this->setDangerAlert(translate('{0} not found', array('User')));

                return $this->redirectToRoute('user_index');
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
     * @return Response
     */
    public function updateAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Update user
            $user = UserModel::update(array(
                    'email' => $postData->get('email'),
                    'firstname' => $postData->get('firstname'),
                    'lastname' => $postData->get('lastname'),
                    'role_id' => $postData->get('role_id'),
                    ), $postData->get('user_id'));

            if ($user->validate() && $user->save()) {
                $this->setSuccessAlert(translate('{0} successful updated', array('User')));
            } else {
                $this->setDangerAlert(translate('Update failed'));
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
     * @return Response
     */
    public function updatePasswordAction($args)
    {
        try {

            // Get post data
            $postData = $this->getRequest()->getPostData();

            // Update user
            $user = UserModel::update(array(
                    'password' => $postData->get('password'),
                    'password2' => $postData->get('password2'),
                    ), $postData->get('user_id'));

            $user->reset_when = null;
            $user->reset_key = null;

            if ($user->validatePassword() && $user->save()) {
                $this->setSuccessAlert(translate('{0} successful updated', array('Password')));
            } else {
                $this->setDangerAlert(translate('Update failed'));
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
     * @return Response
     */
    public function deleteAction($args)
    {
        // Delete user
        $result = UserModel::deleteById($args['id']);

        if ($result) {
            $this->setSuccessAlert(translate('{0} successful deleted', array('User')));
        } else {
            $this->setDangerAlert(translate('Delete failed'));
        }

        return $this->redirectToRoute('user_index');
    }
}
