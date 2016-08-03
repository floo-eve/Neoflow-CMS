<?php

namespace Neoflow\CMS\Service;

use \Neoflow\CMS\Model\UserModel;
use \Neoflow\Framework\Core\AbstractService;

class UserService extends AbstractService
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
        return UserModel::orm()
                ->where('email', '=', $email)
                ->where('password', '=', sha1($password))
                ->fetch();
    }
}
