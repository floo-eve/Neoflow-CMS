<?php

namespace Neoflow\CMS\Controller;

use Neoflow\CMS\App;
use Neoflow\CMS\Core\AbstractController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\UserModel;
use Neoflow\CMS\Views\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\Support\Validation\ValidationException;

class BackendController extends AbstractController
{

    /**
     * Constrcutor.
     *
     * @param App $app
     */
    public function __construct()
    {
        parent::__construct();

        $languages = LanguageModel::repo()
            ->where('is_active', '=', true)
            ->fetchAll();

        // Get current language
        $currentLanguageCode = $this->translator()->getCurrentLanguageCode();
        $currentLanguage = LanguageModel::findByColumn('code', $currentLanguageCode);

        $this->view
            ->set('languages', $languages)
            ->set('currentLanguage', $currentLanguage);
    }

    /**
     * Index action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function indexAction($args)
    {
        return $this->redirectToRoute('dashboard_index');
    }

    /**
     * Logout action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function logoutAction($args)
    {
        if ($this->service('auth')->logout()) {
            return $this
                    ->setSuccessAlert('Logout successful')
                    ->redirectToRoute('backend_login');
        }

        return $this
                ->setDangerAlert('Logout failed')
                ->redirectToRoute('dashboard_index');
    }

    /**
     * Login action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function loginAction($args)
    {
        return $this->render('backend/login');
    }

    /**
     * Authentication and authorization action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function authAction($args)
    {

        // Get post data
        $email = $this->getRequest()->getPost('email');
        $password = $this->getRequest()->getPost('password');

        // Authenticate and authorize user
        if ($this->service('auth')->login($email, $password)) {
            return $this
                    ->setSuccessAlert('Login successful')
                    ->redirectToRoute('dashboard_index');
        }

        return $this
                ->setWarningAlert('Email address and/or password are invalid')
                ->redirectToRoute('backend_login');
    }

    /**
     * Lost password Action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function lostPasswordAction($args)
    {
        return $this->render('backend/lost-password');
    }

    /**
     * New password Action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function newPasswordAction($args)
    {
        $user = UserModel::findByColumn('reset_key', $args['reset_key']);

        if ($user) {

            return $this->render('backend/new-password', array(
                    'user' => $user
            ));
        }
        return $this
                ->setDangerAlert('User not found')
                ->redirectToRoute('backend_login');
    }

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

            if ($user->validatePassword() && $user->save()) {

                $user->reset_when = null;
                $user->reset_key = null;
                $user->save();

                $this->setSuccessAlert('{0} successful updated', array('Password'));
            } else {
                $this->setDangerAlert('Update failed');
            }
        } catch (ValidationException $ex) {
            return $this
                    ->setDangerAlert($ex->getErrors())
                    ->redirectToRoute('backend_new_password', array('reset_key' => $user->reset_key));
        }

        return $this->redirectToRoute('backend_login');
    }

    /**
     * Forgot password action.
     *
     * @param array $args
     *
     * @return Response
     */
    public function resetPasswordAction($args)
    {
        $email = $this->getRequest()->getPost('email');

        $user = UserModel::repo()
            ->where('email', '=', $email)
            ->fetch();

        if ($user) {
            if (1 === 1 || !$user->reset_key || $user->reset_when < microtime(true) - 60 * 60) {
                if ($user->setResetKey() && $user->save()) {
                    $link = $this->router()->generateUrl('backend_new_password', array('reset_key' => $user->reset_key));
                    $message = $this->translator()->translate('Password reset email message', array($user->getFullName(), $link));
                    $subject = $this->translator()->translate('Password reset email subject');

                    $this
                        ->service('mail')
                        ->create($user->email, $subject, $message)
                        ->send();

                    $this->setSuccessAlert('Email successful sent');
                }
            } else {
                $this->setInfoAlert('Email already sent, you can reset your password once per hour');
            }
        } else {
            $this->setWarningAlert('User not found');
        }
        return $this->redirectToRoute('backend_lost_password');
    }

    /**
     * Pre hook method.
     *
     * @param array $args
     *
     * @return Response|bool
     */
    public function preHook($args)
    {
        $currentRoute = $this->router()->getCurrentRouting('route');

        $anonymousRoutes = array('backend_login', 'backend_auth', 'backend_lost_password', 'backend_reset_password', 'backend_new_password', 'backend_update_password');

        if (!$this->service('auth')->isAuthenticated() && !in_array($currentRoute[0], $anonymousRoutes)) {
            return $this->redirectToRoute('backend_login');
        }

//        if (!$this->checkAccess() && !in_array($currentRoute[0], array('backend_login', 'backend_auth', 'backend_forgot'))) {
//        } elseif ($this->checkAccess() && in_array($currentRoute[0], array('backend_login', 'backend_auth', 'backend_forgot'))) {
//            return $this->redirectToRoute('dashboard_index');
//        }

        return false;
    }

    /**
     * Set view.
     */
    protected function setView()
    {
        $this->view = new BackendView();
    }
}
