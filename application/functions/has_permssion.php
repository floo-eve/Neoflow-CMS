<?php
use Neoflow\CMS\App;

/**
 * Check wether authenticated user has permission.
 *
 * @param string|array $permissionKeys
 *
 * @return bool
 */
function has_permission($permissionKeys)
{
    return App::instance()->service('auth')->hasPermission($permissionKeys);
}
