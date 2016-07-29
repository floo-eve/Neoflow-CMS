<?php

namespace Neoflow\CMS\Service;

use \Neoflow\CMS\Model\UserModel;
use \Neoflow\CMS\Repository\UserRepository;
use \Neoflow\Framework\Core\AbstractService;

class UserService extends AbstractService
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

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
        return $this->userRepository
                ->where('email', '=', $email)
                ->where('password', '=', sha1($password))
                ->fetch();
    }
}
