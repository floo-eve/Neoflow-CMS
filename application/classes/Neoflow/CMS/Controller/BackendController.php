<?php

namespace Neoflow\CMS\Controller;

use Neoflow\CMS\App;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Service\UserService;
use Neoflow\CMS\Views\BackendView;
use Neoflow\Framework\Core\AbstractController;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Support\Alert\SuccessAlert;
use Neoflow\Support\Alert\WarningAlert;
use Neoflow\Support\Validation\ValidationService;

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
        $currentLanguageCode = $this->app()->get('translator')->getCurrentLanguageCode();
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
        return $this->render('backend/login');
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
        // Get post data
        $postData = $this->getRequest()->getPostData();

        if ($postData->exists('email') && $postData->exists('password')) {
            $user = $this->service('authentication')->authenticate($postData->get('email'), $postData->get('password'));
            if ($user) {
                $alert = new SuccessAlert('Hallo ' . $user->firstname . ' ' . $user->lastname . ', du hast dich erfolgreich eingeloggt');
                $this->getSession()
                    ->set('user_id', $user->id())
                    ->setFlash('alert', $alert);

                return $this->redirectToRoute('dashboard_index');
            }
            $alert = new WarningAlert('E-Mailadresse und/oder Password falsch');
            $this->setFlash('alert', $alert);

            return $this->redirectToRoute('backend_login');
        }

        return $this->redirectToRoute('backend_login');
    }

    public function forgotAction($args)
    {
        $result = $this->service('authentication')->resetPassword('jonathan.nessier@outlook.com');

        if ($result) {
            'yeah';
        } else {
            'nope';
        }
        exit;


        //return $this->render('backend/login');
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
        if (!$this->checkAccess() && !in_array($currentRoute[0], array('backend_login', 'backend_auth', 'backend_forgot'))) {
            return $this->redirectToRoute('backend_login');
        } elseif ($this->checkAccess() && in_array($currentRoute[0], array('backend_login', 'backend_auth', 'backend_forgot'))) {
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
