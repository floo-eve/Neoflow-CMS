<?php

namespace Neoflow\CMS\Model;

use Neoflow\Framework\ORM\AbstractEntityModel;
use Neoflow\Framework\ORM\EntityRepository;

class PermissionModel extends AbstractEntityModel
{
    /**
     * @var string
     */
    public static $tableName = 'permissions';

    /**
     * @var string
     */
    public static $primaryKey = 'permission_id';

    /**
     * @var array
     */
    public static $properties = ['permission_id', 'title', 'description', 'tag'];

    /**
     * Get repository to fetch roles.
     *
     * @return EntityRepository
     */
    public function roles()
    {
        return $this->hasManyThrough('\\Neoflow\\CMS\\Model\\RoleModel', '\\Neoflow\\CMS\\Model\\RolePermissionModel', 'permission_id', 'role_id');
    }
}
