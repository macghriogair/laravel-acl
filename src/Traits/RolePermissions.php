<?php
/**
 * @date 22.11.2016
 * @file    RolePermissions.php
 * @author Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

namespace Macgriog\Acl\Traits;

trait RolePermissions
{
    use PermissionStrategy;

    /**
     * Role permissions do not get merged with any higher level.
     *
     * @return array
     */
    public function getMergedPermissions() : array
    {
        return $this->permissions;
    }

    /**
     * Returns if the role has access to any of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyAccess(array $permissions) : bool
    {
        return $this->hasPermission($permissions, false);
    }
}
