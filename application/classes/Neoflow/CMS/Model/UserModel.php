<?php

namespace Neoflow\CMS\Model;

use Exception;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Framework\Support\Validation\Validator;

class UserModel extends AbstractEntityModel {

    /**
     * @var string
     */
    public static $tableName = 'users';

    /**
     * @var string
     */
    public static $primaryKey = 'user_id';

    /**
     * @var array
     */
    public static $properties = ['user_id', 'email', 'firstname', 'lastname', 'role_id', 'reset_key', 'reseted_when'];

    /**
     * @var array
     */
    public static $hiddenProperties = ['password', 'reset_key', 'reseted_when'];

    /**
     * Get repository to fetch role.
     *
     * @return EntityRepository
     */
    public function role() {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\RoleModel', 'role_id');
    }

    /**
     * Validate user entity.
     *
     * @return bool
     */
    public function validate() {
        $validator = new Validator($this->data);

        $validator
                ->required()
                ->email()
                ->callback(function ($email, $user) {
                    $users = UserModel::repo()
                            ->where('email', '=', $email)
                            ->where('user_id', '!=', $user->id())
                            ->fetchAll();

                    return $users->count() === 0;
                }, '{0} has to be unique', array($this))
                ->set('email', 'Email address');

        $validator
                ->maxLength(50)
                ->set('firstname', 'Firstname');

        $validator
                ->maxLength(50)
                ->set('lastname', 'Lastname');

        $validator
                ->required()
                ->set('role_id', 'Role');

        if ($this->password && $this->password2) {
            $this->validatePassword();
        }

        return $validator->validate();
    }

    /**
     * Validate password of user entity
     *
     * @return array
     */
    public function validatePassword() {
        $validator = new Validator(array(
            'password' => $this->password,
            'password2' => $this->password2,
            self::$primaryKey => $this->id(),
        ));

        $validator
                ->required()
                ->set('password2', 'Confirm password');

        $validator
                ->required()
                ->minLength(6)
                ->callback(function ($password, $password2) {
                    return $password === $password2;
                }, 'Password is not matching confirm password', array($this->password2))
                ->set('password', 'Password');

        return $validator->validate();
    }

    /**
     * Get fullname (firstname lastname).
     *
     * @return string
     */
    public function getFullname() {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Save user entity.
     *
     * @return bool
     */
    public function save() {
        // Prevent role change of initial user by removing role_id
        if ($this->id() === 1) {
            $this->remove('role_id');
        }

        return parent::save();
    }

    public function delete() {
        // Prevent delete of initial user
        if ($this->id() != 1) {
            return parent::delete();
        }

        return false;
    }

    /**
     * Generate and set reset key
     *
     * @param bool $reset
     *
     * @return self
     */
    public function setResetKey($reset = false) {
        if ($reset) {
            $this->reset_key = null;
            $this->reseted_when = null;
        } else {
            $this->reset_key = sha1(uniqid());
            $this->reseted_when = microtime(true);
        }

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @param string $password2
     * @return self
     */
    public function setPassword($password, $password2) {
        return $this
                        ->addProperty('password')
                        ->set('password', $password)
                        ->addProperty('password2')
                        ->set('password2', $password2);
    }

    /**
     * Update password of user entity
     *
     * @param string $password
     * @param string $password2
     * @param int|string $id
     *
     * @return self
     *
     * @throws Exception
     */
    public static function updatePassword($password, $password2, $id) {
        $user = self::findById($id);
        if ($user) {
            return $user
                            ->setPassword($password, $password2)
                            ->setResetKey(true);
        }
        throw new Exception('User not found');
    }

}
