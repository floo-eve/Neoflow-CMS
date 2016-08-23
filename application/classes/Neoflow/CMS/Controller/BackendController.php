<?php

namespace Neoflow\CMS\Controller;

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
     * Constructor.
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
                    ->setSuccessAlert(translate('Logout successful'))
                    ->redirectToRoute('backend_login');
        }

        return $this
                ->setDangerAlert(translate('Logout failed'))
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
                    ->setSuccessAlert(translate('Login successful'))
                    ->redirectToRoute('dashboard_index');
        }

        return $this
                ->setWarningAlert(translate('Email address and/or password are invalid'))
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
     * @return Response|RedirectResponse
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
                ->setDangerAlert(translate('User not found'))
                ->redirectToRoute('backend_login');
    }

    /**
     * Update password action
     *
     * @param array $args
     * @return RedirectResponse
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

            if ($user->reset_key === $postData->get('reset_key') && $user->validatePassword() && $user->save()) {

                $user->reseted_when = null;
                $user->reset_key = null;
                $user->save();

                $this->setSuccessAlert(translate('{0} successful updated', array('Password')));
            } else {
                $this->setDangerAlert(translate('Update failed'));
            }
        } catch (ValidationException $ex) {
            return $this
                    ->setDangerAlert($ex->getErrors())
                    ->redirectToRoute('backend_new_password', array('reset_key' => $user->reset_key));
        }

        return $this->redirectToRoute('backend_login');
    }

    /**
     * Reset password action.
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
            if (1 === 1 || !$user->reset_key || $user->reseted_when < microtime(true) - 60 * 60) {
                if ($user->setResetKey() && $user->save()) {
                    $link = generate_url('backend_new_password', array('reset_key' => $user->reset_key));
                    $message = translate('Password reset email message', array($user->getFullName(), $link));
                    $subject = translate('Password reset email subject');

                    $this
                        ->service('mail')
                        ->create($user->email, $subject, $message)
                        ->send();

                    $this->setSuccessAlert(translate('Email successful sent'));
                }
            } else {
                $this->setInfoAlert(translate('Email already sent, you can reset your password once per hour'));
            }
        } else {
            $this->setWarningAlert(translate('User not found'));
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
        } elseif ($this->service('auth')->isAuthenticated() && in_array($currentRoute[0], $anonymousRoutes)) {
            return $this->redirectToRoute('dashboard_index');
        }

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
