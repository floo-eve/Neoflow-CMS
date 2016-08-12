<?php

namespace Neoflow\CMS\Model;

use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Support\Validation\Validator;

class UserModel extends AbstractEntityModel
{

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
    public static $properties = ['user_id', 'email', 'firstname', 'lastname', 'role_id'];

    /**
     * @var array
     */
    public static $saveProperties = ['user_id', 'email', 'firstname', 'lastname', 'role_id', 'password'];

    /**
     * Get repository to fetch role.
     *
     * @return EntityRepository
     */
    public function role()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\RoleModel', 'role_id');
    }

    /**
     * Validate user entity.
     *
     * @return bool
     */
    public function validate()
    {
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

    public function validatePassword()
    {
        $validator = new Validator(array(
            'password' => $this->password,
            'password2' => $this->password2
        ));

        $validator
            ->required()
            ->set('password2', 'Confirm password');

        $validator
            ->required()
            ->minLength(6)
            ->callback(function($password, $password2) {
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
    public function getFullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Save user entity.
     *
     * @return bool
     */
    public function save()
    {
        // Prevent role change of initial user by removing role_id
        if ($this->id() === 1) {
            $this->remove('role_id');
        }

        // Remove confirm password property
        $this->remove('password2');

        // Calculate password to sha1 hash
        if ($this->password) {
            $password = sha1($this->password);
            $this
                ->addProperty('password')
                ->set('password', $password);
        }

        return parent::save();
    }

    public function delete()
    {
        // Prevent delete of initial user
        if ($this->id() !== 1) {
            return parent::delete();
        }
        return false;
    }
}
