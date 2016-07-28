<?php

namespace Neoflow\CMS\Model;

use Neoflow\Framework\Core\AbstractModel;

class RolePermissionModel extends AbstractModel
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

    public function permission()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PermissionModel', 'permission_id');
    }

    public function role()
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\RoleModel', 'role_id');
    }
}
