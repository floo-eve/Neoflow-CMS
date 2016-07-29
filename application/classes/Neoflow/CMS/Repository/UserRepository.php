<?php

namespace Neoflow\CMS\Repository;

use \Neoflow\CMS\Model\UserModel;
use \Neoflow\Framework\Core\AbstractRepository;

class UserRepository extends AbstractRepository
{

    /**
     * @var string
     */
    public $modelClassName = '\\Neoflow\\CMS\\Model\\UserModel';

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
        return $this
                ->where('email', '=', $email)
                ->where('password', '=', sha1($password))
                ->fetch();
    }
}
