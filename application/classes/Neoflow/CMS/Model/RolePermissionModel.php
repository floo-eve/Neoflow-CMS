<?php

namespace Neoflow\CMS\Model;

use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;

class RolePermissionModel extends AbstractEntityModel
{
    /**
     * @var string
     */
    public static $tableName = 'roles_permissions';

    /**
     * @var string
     */
    public static $primaryKey = 'role_permission_id';

    /**
     * @var array
     */
    public static $properties = ['role_permission_id', 'role_id', 'permission_id'];

    /**
     * Get repository to fetch permissions.
     *
     * @return EntityRepository
     */
    public function permission()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PermissionModel', 'permission_id');
    }

    /**
     * Get repository to fetch role.
     *
     * @return EntityRepository
     */
    public function role()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\RoleModel', 'role_id');
    }
}
