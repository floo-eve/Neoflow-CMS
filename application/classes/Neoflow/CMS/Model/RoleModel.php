<?php

namespace Neoflow\CMS\Model;

use Exception;
use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;
use Neoflow\Support\Validation\Validator;

class RoleModel extends AbstractEntityModel
{
    /**
     * @var string
     */
    public static $tableName = 'roles';

    /**
     * @var string
     */
    public static $primaryKey = 'role_id';

    /**
     * @var array
     */
    public static $properties = ['role_id', 'title', 'description'];

    /**
     * Get repository to fetch permissions.
     *
     * @return EntityRepository
     */
    public function permissions()
    {
        return $this->hasManyThrough('\\Neoflow\\CMS\\Model\\PermissionModel', '\\Neoflow\\CMS\\Model\\RolePermissionModel', 'role_id', 'permission_id');
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
            ->betweenLength(3, 50)
            ->callback(function ($title, $role) {
                $roles = RoleModel::repo()
                    ->where('title', '=', $title)
                    ->where('role_id', '!=', $role->id())
                    ->fetchAll();

                return $roles->count() === 0;
            }, '{0} has to be unique', array($this))
            ->set('title', 'Title');

        $validator
            ->maxLength(150)
            ->set('description', 'Description');

        $validator
            ->required()
            ->set('role_id', 'Role');

        return $validator->validate();
    }

    /**
     * Save role.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function save($validate = true)
    {
        if ($this->id() != 1 && parent::save($validate)) {

            // Delete old role permissions
            RolePermissionModel::deleteAllByColumn('role_id', $this->id());

            // Create new role permissions
            foreach ($this->permission_ids as $permission_id) {
                RolePermissionModel::create(array(
                    'role_id' => $this->id(),
                    'permission_id' => $permission_id,
                ));
            }

            return true;
        }

        return false;
    }

    /**
     * Delete role.
     *
     * @return bool
     */
    public function delete()
    {
        if ($this->id() != 1) {
            $rolePermissions = RolePermissionModel::findAllByColumn('role_id', $this->id());

            if ($rolePermissions->delete()) {
                return parent::delete();
            }
        }

        return false;
    }

    /**
     * Getter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $value = parent::__get($name);

        if (!$value && $name === 'permission_ids') {
            $value = $this->permissions()->fetchAll()->map('permission_id')->toArray();

            $this->set('permission_ids', $value);
        }

        return $value;
    }
}
