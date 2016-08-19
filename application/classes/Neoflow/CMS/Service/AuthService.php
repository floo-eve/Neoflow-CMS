<?php

namespace Neoflow\CMS\Service;

use Exception;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\Core\AbstractService;

class AuthService extends AbstractService
{

    /**
     * Authenticate and authorize user by email address and password.
     *
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    public function login($email, $password)
    {
        if ($this->authenticate($email, $password)) {
            return $this->authorize();
        }
        return false;
    }

    /**
     * Logout authenticated user.
     *
     * @return bool
     */
    public function logout()
    {
        $this->session()->restart();

        return true;
    }

    /**
     * Check wether a user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->session()->exists('_USER');
    }

    /**
     * Get authenticated user.
     *
     * @return UserModel
     */
    public function getAuthenticatedUser()
    {
        return $this->session()->get('_USER');
    }

    /**
     * Check wether authenticated user has permission.
     *
     * @param string $permissionKey
     *
     * @return bool
     */
    public function hasPermission($permissionKey)
    {
        return in_array($permissionKey, $this->getPermissionKeys());
    }

    /**
     * Authenticate user with email and password.
     *
     * @param string $email
     * @param string $password
     * @param bool   $authorize
     *
     * @return bool
     */
    protected function authenticate($email, $password)
    {
        $user = UserModel::repo()
            ->where('email', '=', $email)
            ->where('password', '!=', '')
            ->where('password', '=', sha1($password))
            ->fetch();

        if ($user) {
            $user->setReadOnly();
            $this->session()->set('_USER', $user);

            return true;
        }

        return false;
    }

    /**
     * Authorize authenticated user.
     *
     * @param UserModel $user
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function authorize()
    {
        $user = $this->getAuthenticatedUser();

        if ($user) {
            $role = $user
                ->role()
                ->fetch();

            $permissions = $role
                ->permissions()
                ->fetchAll();

            $permissionKeys = $permissions->map(function ($permission) {
                    return $permission->permission_key;
                })->toArray();

            $this->session()->set('_PERMISSION_KEYS', $permissionKeys);

            return true;
        }
        throw new Exception('Authentication failed, no user authenticated');
    }

    /**
     * Get permission keys of authenticated user.
     *
     * @return array
     */
    protected function getPermissionKeys()
    {
        return $this->session()->get('_PERMISSION_KEYS');
    }
}
