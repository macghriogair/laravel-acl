<?php
/**
 * @date 22.11.2016
 * @file    UserPermissions.php
 * @author Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

namespace Macgriog\Acl\Traits;

trait UserPermissions
{
    use PermissionStrategy;

    /**
     * @var array Allowed permissions values.
     *
     * Possible options:
     *    0 => Remove.
     *    1 => Add.
     *    -1 => Force deny when merging.
     *
     * @return array
     */
    public function getAllowedPermissionsValues() : array
    {
        return [-1, 0, 1];
    }

    /**
     * User permissions get merged with all role permissions the user belongs to.
     *
     * @return array
     */
    public function getMergedPermissions() : array
    {
        $mergedPermissions = [];

        foreach ($this->getRoles() as $role) {
            if (!is_array($role->permissions)) {
                continue;
            }
            $mergedPermissions = array_merge($mergedPermissions, $role->permissions);
        }

        if (is_array($this->permissions)) {
            $mergedPermissions = array_merge($mergedPermissions, $this->permissions);
        }

        return $mergedPermissions;
    }

    /**
     * See if a user has access to the passed permission(s).
     * Permissions are merged from all roles the user belongs to
     * and then are checked against the passed permission(s).
     *
     * If multiple permissions are passed, the user must
     * have access to all permissions passed through, unless the
     * "all" flag is set to false.
     *
     * Root users have access no matter what.
     *
     * @param  string|array  $permissions
     * @param  bool  $all
     * @return bool
     */
    public function hasAccess($permissions, $all = true) : bool
    {
        if ($this->isRoot()) {
            return true;
        }

        return $this->hasPermission($permissions, $all);
    }

    /**
     * Returns if the user has access to any of the given permissions.
     *
     * @param  array  $permissions
     * @return bool
     */
    public function hasAnyAccess(array $permissions) : bool
    {
        return $this->hasAccess($permissions, false);
    }

    /**
     * Checks if the user is a ROOT user,
     * i.e. has access to everything regardless of permissions.
     *
     * @return bool
     */
    public function isRoot() : bool
    {
        return (bool) $this->is_root;
    }
}
