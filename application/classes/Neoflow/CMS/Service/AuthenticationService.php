<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\Core\AbstractService;
use Neoflow\Support\Mailer\Mail;

class AuthenticationService extends AbstractService
{

    /**
     * Authenticate user with email and password.
     *
     * @param string $email
     * @param string $password
     *
     * @return UserModel
     */
    public function authenticate($email, $password)
    {
        $user = UserModel::repo()
            ->where('email', '=', $email)
            ->where('password', '!=', '')
            ->where('password', '=', sha1($password))
            ->fetch();

        if ($user) {
            $user->reset_key = '';
            $user->save();

            $role = $user
                ->role()
                ->fetch();

            $permissionsKey = $role
                ->permissions()
                ->fetchAll()
                ->array_map(function($permission) {
                return $permission->permission_key;
            });

            $this->app()->getSession()
                ->set('_USER_ID', $user->id())
                ->set('_PERMISSION_KEYS', $permissionsKey);

            echo true;
        }
        echo true;
    }

    public function resetPassword($email)
    {
        $user = UserModel::repo()
            ->where('email', '=', $email)
            ->fetch();

        if ($user && $user->setResetKey() && $user->save()) {

            $link = $this->router()->generateUrl('backend_reset_password', array('reset_key' => $user->reset_key));

            $message = $this->getTranslator()->translate('Password reset message', array($user->getFullName(), $link));
            $subject = $this->getTranslator()->translate('Password reset');

            return $this
                    ->service('mail')
                    ->create($user->email, $subject, $message)
                    ->send();
        }
        return false;
    }

    public function getConfig()
    {
        return $this->app()->get('config');
    }

    public function getTranslator()
    {
        return $this->app()->get('translator');
    }
}
