<?php

namespace Neoflow\CMS\Model;

use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityCollection;
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
     * Get repository to fetch role.
     *
     * @return EntityRepository
     */
    public function role()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\RoleModel', 'role_id');
    }

    /**
     * Get user permissions by his role.
     *
     * @return EntityCollection
     */
    public function getPermissions()
    {
        $role = $this->role()->fetch();

        return $role->permissions()->fetchAll();
    }

    /**
     * Get fullname (firstname lastname).
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->firstname.' '.$this->lastname;
    }

    /**
     * Validate setting entity.
     *
     * @return bool
     */
    public function validate()
    {
        $validator = new Validator($this->toArray());

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

        return $validator->validate();
    }
}
