<?php

namespace Lahaina\CMS\Mapper;

use \Lahaina\CMS\Model\UserModel;
use \Lahaina\Framework\Core\AbstractMapper;

class UserMapper extends AbstractMapper
{

    /**
     * @var string
     */
    public static $modelClassName = '\\Lahaina\\CMS\\Model\\UserModel';

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
        return $this->getOrm()
                ->where('email', '=', $email)
                ->where('password', '=', sha1($password))
                ->fetch();
    }
}
