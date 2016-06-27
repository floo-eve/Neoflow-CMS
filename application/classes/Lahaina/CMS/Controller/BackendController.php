<?php

namespace Lahaina\CMS\Controller;

use Lahaina\CMS\App;
use Lahaina\CMS\Mapper\LanguageMapper;
use Lahaina\CMS\Mapper\UserMapper;
use Lahaina\CMS\Views\BackendView;
use Lahaina\CMS\Core\AbstractController;
use Lahaina\Framework\HTTP\Responsing\RedirectResponse;
use Lahaina\Framework\HTTP\Responsing\Response;
use Lahaina\Helper\Alert\SuccessAlert;
use Lahaina\Helper\Alert\WarningAlert;

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

        $languageMapper = new LanguageMapper();
        $languages = $languageMapper->getOrm()
            ->where('is_active', '=', true)
            ->fetchAll();
        // Get current language
        $currentLanguageCode = $this->app()->get('translator')->getCurrentLanguageCode();
        $currentLanguage = $languageMapper->findByCode($currentLanguageCode);

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
        $this->getSession()
            ->restart()
            ->setFlash('alert', new SuccessAlert('Erfolgreich ausgeloggt'));

        return $this->redirectToRoute('backend_login');
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
        return $this->render('backend/login', array(
                'message' => 'Login',
                'pageHeaderTitle' => 'Dashboard',));
    }

    /**
     * Authentication action.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function authAction($args)
    {
        $postData = $this->getRequest()->getPostData();
        $session = $this->getSession();
        $userMapper = new UserMapper();
        if ($postData->exists('email') && $postData->exists('password')) {
            $user = $userMapper->authenticate($postData->get('email'), $postData->get('password'));
            if ($user) {
                $alert = new SuccessAlert('Hallo ' . $user->firstname . ' ' . $user->lastname . ', du hast dich erfolgreich eingeloggt');
                $session
                    ->set('user_id', $user->id())
                    ->setFlash('alert', $alert);

                return $this->redirectToRoute('dashboard_index');
            }
            $alert = new WarningAlert('E-Mailadresse und/oder Password falsch');
            $session->setFlash('alert', $alert);

            return $this->redirectToRoute('backend_login');
        }

        return $this->redirectToRoute('backend_login');
    }

    /**
     * Check user access.
     *
     * @return bool
     */
    protected function checkAccess()
    {
        // Actually only check if user is logged in (user_id exists)
        return $this->getSession()->has('user_id');
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
        $currentRouting = $this->app()->get('router')->getCurrentRouting();
        $currentRoute = $currentRouting[0];
        if (!$this->checkAccess() && !in_array($currentRoute[0], array('backend_login', 'backend_auth'))) {
            return $this->redirectToRoute('backend_login');
        } elseif ($this->checkAccess() && in_array($currentRoute[0], array('backend_login', 'backend_auth'))) {
            return $this->redirectToRoute('dashboard_index');
        }

        return false;
    }

    /**
     * Set view.
     */
    public function setView()
    {
        $this->view = new BackendView();
    }
}
